document.addEventListener('DOMContentLoaded', () => {
  const clock = document.getElementById('clock');
  if (clock) {
    const updateClock = () => {
      const now = new Date();
      const time = now.toLocaleTimeString('id-ID', { hour12: false });
      clock.textContent = time;
    };
    updateClock();
    setInterval(updateClock, 1000);
  }

  const toastData = document.getElementById('toastData');
  if (toastData && toastData.dataset.message) {
    showToast(toastData.dataset.message, 'success');
  }

  setupAudioExperience();
});

function showToast(message, type = 'success') {
  if (typeof Swal !== 'undefined') {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: type,
      title: message,
      showConfirmButton: false,
      timer: 2200,
      timerProgressBar: true,
      customClass: {
        popup: 'swal2-toast-custom'
      }
    });
  }
}

function setupAudioExperience() {
  const route = window.location.pathname || '';
  const isQuiz = route.includes('/pages/quiz.php');
  const isResult = route.includes('/pages/result.php');

  window.audioSystem = window.audioSystem || createAudioSystem();

  window.audioSystem.bindButtonHovers();
  window.audioSystem.bindLogoShimmer();

  if (isResult) {
    window.audioSystem.startMenuBgm();
    window.audioSystem.playApplause();
    return;
  }

  if (!isQuiz) {
    window.audioSystem.playIntro('menu');
  }

  window.addEventListener('pointerdown', () => {
    window.audioSystem.resumeAudio().then(() => {
      if (isQuiz) {
        window.audioSystem.startQuizBgm();
        return;
      }
      window.audioSystem.startMenuBgm();
    });
  }, { once: true });
}

function createAudioSystem() {
  let audioCtx = null;
  let currentLoop = null;
  let introPlayed = false;
  let tickingInterval = null;
  let activeTrack = null;

  const playTone = (freq, duration = 0.12, type = 'sine', gainValue = 0.08, startOffset = 0) => {
    resumeAudio().then(() => {
      if (!audioCtx) return;
      const now = audioCtx.currentTime + startOffset;
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      const filter = audioCtx.createBiquadFilter();
      osc.type = type;
      osc.frequency.value = freq;
      filter.type = 'lowpass';
      filter.frequency.value = 1800;
      osc.connect(filter);
      filter.connect(gain);
      gain.connect(audioCtx.destination);
      gain.gain.setValueAtTime(0.0001, now);
      gain.gain.exponentialRampToValueAtTime(gainValue, now + 0.02);
      gain.gain.exponentialRampToValueAtTime(0.0001, now + duration);
      osc.start(now);
      osc.stop(now + duration + 0.02);
    });
  };

  const ensureAudioContext = () => {
    if (!audioCtx) {
      const AudioContext = window.AudioContext || window.webkitAudioContext;
      if (!AudioContext) return null;
      audioCtx = new AudioContext();
    }
    return audioCtx;
  };

  async function resumeAudio() {
    const ctx = ensureAudioContext();
    if (!ctx) return null;
    if (ctx.state === 'suspended') {
      try {
        await ctx.resume();
      } catch (error) {
        console.warn('Audio tidak dapat dimulai.', error);
      }
    }
    return ctx;
  }

  const stopLoop = () => {
    if (currentLoop) {
      clearTimeout(currentLoop);
      currentLoop = null;
    }
    activeTrack = null;
  };

  const stopTicking = () => {
    if (tickingInterval) {
      clearInterval(tickingInterval);
      tickingInterval = null;
    }
  };

  const schedulePattern = (notes, repeat = true) => {
    if (!audioCtx) return;
    const now = audioCtx.currentTime;
    let nextTime = now + 0.03;

    notes.forEach((note) => {
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      const filter = audioCtx.createBiquadFilter();
      osc.type = note.type || 'triangle';
      osc.frequency.value = note.freq;
      filter.type = 'lowpass';
      filter.frequency.value = note.filter || 1800;
      osc.connect(filter);
      filter.connect(gain);
      gain.connect(audioCtx.destination);
      gain.gain.setValueAtTime(0.0001, nextTime);
      gain.gain.exponentialRampToValueAtTime(note.gain || 0.05, nextTime + 0.03);
      gain.gain.exponentialRampToValueAtTime(0.0001, nextTime + note.duration);
      osc.start(nextTime);
      osc.stop(nextTime + note.duration + 0.02);
      nextTime += note.duration;
    });

    if (repeat) {
      const wait = Math.max(0.5, notes.reduce((total, note) => total + note.duration, 0));
      currentLoop = setTimeout(() => schedulePattern(notes, repeat), wait * 1000);
    }
  };

  const startMenuBgm = async () => {
    await resumeAudio();
    stopLoop();
    stopTicking();
    activeTrack = 'menu';

    const pattern = [
      { freq: 261.63, duration: 0.26, gain: 0.045, type: 'triangle', filter: 1400 },
      { freq: 329.63, duration: 0.24, gain: 0.04, type: 'triangle', filter: 1600 },
      { freq: 392.0, duration: 0.28, gain: 0.046, type: 'triangle', filter: 1700 },
      { freq: 523.25, duration: 0.42, gain: 0.05, type: 'triangle', filter: 1900 },
      { freq: 392.0, duration: 0.22, gain: 0.043, type: 'triangle', filter: 1700 },
      { freq: 329.63, duration: 0.22, gain: 0.04, type: 'triangle', filter: 1600 },
      { freq: 261.63, duration: 0.5, gain: 0.042, type: 'triangle', filter: 1400 },
      { freq: 196.0, duration: 0.24, gain: 0.038, type: 'triangle', filter: 1200 }
    ];

    schedulePattern(pattern, true);
  };

  const startQuizBgm = async () => {
    await resumeAudio();
    stopLoop();
    stopTicking();
    activeTrack = 'quiz';

    const pattern = [
      { freq: 110.0, duration: 0.32, gain: 0.03, type: 'sawtooth', filter: 900 },
      { freq: 130.81, duration: 0.28, gain: 0.032, type: 'sawtooth', filter: 1100 },
      { freq: 146.83, duration: 0.32, gain: 0.034, type: 'sawtooth', filter: 1150 },
      { freq: 174.61, duration: 0.38, gain: 0.036, type: 'sawtooth', filter: 1200 },
      { freq: 146.83, duration: 0.28, gain: 0.034, type: 'sawtooth', filter: 1100 },
      { freq: 130.81, duration: 0.3, gain: 0.03, type: 'sawtooth', filter: 1000 }
    ];

    schedulePattern(pattern, true);
  };

  const playIntro = async (mode = 'menu') => {
    if (introPlayed) return;
    const ctx = await resumeAudio();
    if (!ctx) return;

    introPlayed = true;
    const introNotes = [
      { freq: 220.0, duration: 0.38, gain: 0.03, type: 'triangle', filter: 1200 },
      { freq: 261.63, duration: 0.42, gain: 0.032, type: 'triangle', filter: 1300 },
      { freq: 329.63, duration: 0.58, gain: 0.035, type: 'triangle', filter: 1500 }
    ];

    const now = ctx.currentTime;
    let nextTime = now + 0.03;
    introNotes.forEach((note) => {
      const osc = ctx.createOscillator();
      const gain = ctx.createGain();
      const filter = ctx.createBiquadFilter();
      osc.type = note.type;
      osc.frequency.value = note.freq;
      filter.type = 'lowpass';
      filter.frequency.value = note.filter;
      osc.connect(filter);
      filter.connect(gain);
      gain.connect(ctx.destination);
      gain.gain.setValueAtTime(0.0001, nextTime);
      gain.gain.linearRampToValueAtTime(note.gain, nextTime + 0.2);
      gain.gain.exponentialRampToValueAtTime(0.0001, nextTime + note.duration);
      osc.start(nextTime);
      osc.stop(nextTime + note.duration + 0.05);
      nextTime += note.duration;
    });

    setTimeout(() => {
      if (mode === 'menu') {
        startMenuBgm();
      } else {
        startQuizBgm();
      }
    }, 1200);
  };

  const playPop = async () => {
    await resumeAudio();
    if (!audioCtx) return;
    const now = audioCtx.currentTime;
    const osc = audioCtx.createOscillator();
    const gain = audioCtx.createGain();
    const filter = audioCtx.createBiquadFilter();
    osc.type = 'sine';
    osc.frequency.value = 720;
    filter.type = 'lowpass';
    filter.frequency.value = 2000;
    osc.connect(filter);
    filter.connect(gain);
    gain.connect(audioCtx.destination);
    gain.gain.setValueAtTime(0.0001, now);
    gain.gain.exponentialRampToValueAtTime(0.06, now + 0.01);
    gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.15);
    osc.start(now);
    osc.stop(now + 0.16);
  };

  const playShimmer = async () => {
    await resumeAudio();
    if (!audioCtx) return;
    const now = audioCtx.currentTime;
    [640, 760, 910].forEach((freq, index) => {
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      const filter = audioCtx.createBiquadFilter();
      osc.type = 'triangle';
      osc.frequency.value = freq;
      filter.type = 'highpass';
      filter.frequency.value = 700;
      osc.connect(filter);
      filter.connect(gain);
      gain.connect(audioCtx.destination);
      const start = now + (index * 0.04);
      gain.gain.setValueAtTime(0.0001, start);
      gain.gain.exponentialRampToValueAtTime(0.03, start + 0.02);
      gain.gain.exponentialRampToValueAtTime(0.0001, start + 0.1);
      osc.start(start);
      osc.stop(start + 0.12);
    });
  };

  const playCorrect = () => {
    playTone(660, 0.12, 'triangle', 0.07);
    setTimeout(() => playTone(880, 0.1, 'triangle', 0.06), 80);
    setTimeout(() => playTone(1046, 0.16, 'triangle', 0.05), 150);
  };

  const playWrong = () => {
    playTone(220, 0.22, 'square', 0.08);
    setTimeout(() => playTone(160, 0.22, 'square', 0.06), 80);
  };

  const playApplause = () => {
    resumeAudio().then(() => {
      if (!audioCtx) return;
      const base = audioCtx.currentTime;
      [420, 520, 680, 840, 960].forEach((freq, index) => {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        const filter = audioCtx.createBiquadFilter();
        osc.type = 'triangle';
        osc.frequency.value = freq;
        filter.type = 'bandpass';
        filter.frequency.value = freq + 200;
        osc.connect(filter);
        filter.connect(gain);
        gain.connect(audioCtx.destination);
        const start = base + (index * 0.08);
        gain.gain.setValueAtTime(0.0001, start);
        gain.gain.exponentialRampToValueAtTime(0.04, start + 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, start + 0.14);
        osc.start(start);
        osc.stop(start + 0.16);
      });

      const noiseBuffer = audioCtx.createBuffer(1, audioCtx.sampleRate * 0.35, audioCtx.sampleRate);
      const noiseData = noiseBuffer.getChannelData(0);
      for (let i = 0; i < noiseData.length; i += 1) {
        noiseData[i] = (Math.random() * 2 - 1) * 0.12;
      }
      const noiseSource = audioCtx.createBufferSource();
      const noiseFilter = audioCtx.createBiquadFilter();
      const noiseGain = audioCtx.createGain();
      noiseSource.buffer = noiseBuffer;
      noiseSource.loop = false;
      noiseFilter.type = 'lowpass';
      noiseFilter.frequency.value = 520;
      noiseGain.gain.setValueAtTime(0.16, base);
      noiseGain.gain.exponentialRampToValueAtTime(0.0001, base + 0.3);
      noiseSource.connect(noiseFilter);
      noiseFilter.connect(noiseGain);
      noiseGain.connect(audioCtx.destination);
      noiseSource.start(base);
      noiseSource.stop(base + 0.35);
    });
  };

  const startTicking = () => {
    stopTicking();
    resumeAudio().then(() => {
      if (!audioCtx) return;
      tickingInterval = setInterval(() => {
        const now = audioCtx.currentTime;
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'square';
        osc.frequency.value = 880;
        gain.gain.setValueAtTime(0.0001, now);
        gain.gain.exponentialRampToValueAtTime(0.03, now + 0.01);
        gain.gain.exponentialRampToValueAtTime(0.0001, now + 0.08);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start(now);
        osc.stop(now + 0.1);
      }, 1000);
    });
  };

  const bindButtonHovers = () => {
    const selectors = 'button, .btn, input[type="submit"], input[type="button"]';
    document.querySelectorAll(selectors).forEach((element) => {
      element.addEventListener('mouseenter', () => {
        playPop();
      }, { passive: true });
      element.addEventListener('focusin', () => {
        playPop();
      }, { passive: true });
    });
  };

  const bindLogoShimmer = () => {
    const targets = document.querySelectorAll('.hero-image-frame img, .auth-hero img, .navbar-brand');
    targets.forEach((target) => {
      target.classList.remove('audio-logo-shimmer');
      target.addEventListener('animationend', () => {
        target.classList.remove('audio-logo-shimmer');
      }, { once: true });
      target.classList.add('audio-logo-shimmer');
      playShimmer();
    });
  };

  return {
    resumeAudio,
    playIntro,
    startMenuBgm,
    startQuizBgm,
    playCorrect,
    playWrong,
    playApplause,
    playPop,
    bindButtonHovers,
    bindLogoShimmer,
    stopLoop,
    stopTicking,
    startTicking,
    playShimmer,
  };
}
