const quizState = {
  currentQuestion: null,
  score: 0,
  correct: 0,
  wrong: 0,
  index: 0,
  total: 10,
  timer: 420,
  interval: null,
  locked: false,
};

const timerElement = document.getElementById('timer');
const questionText = document.getElementById('questionText');
const questionDomain = document.getElementById('questionDomain');
const optionButtons = document.getElementById('optionButtons');
const scoreValue = document.getElementById('scoreValue');
const currentIndex = document.getElementById('currentIndex');
const progressBar = document.getElementById('quizProgress');
const restartButton = document.getElementById('restartQuiz');

function startQuiz() {
  questionText.textContent = 'Memuat soal...';
  questionDomain.textContent = 'Memuat domain...';
  scoreValue.textContent = '0';

  fetch('../process/submit_answer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'start' }),
  })
    .then(res => res.json().then(data => ({ status: res.status, body: data })))
    .then(({ status, body }) => {
      if (status !== 200 || body.error) {
        const message = body.error || 'Gagal memulai kuis. Silakan coba lagi.';
        questionText.textContent = 'Gagal memuat soal.';
        questionDomain.textContent = 'Belum diklasifikasikan';
        showToast(message, 'error');
        return;
      }
      quizState.score = 0;
      quizState.correct = 0;
      quizState.wrong = 0;
      quizState.index = 0;
      quizState.timer = 420;
      quizState.total = body.question.total || 10;
      renderQuestion(body.question);
      window.audioSystem?.startQuizBgm();
      startTimer();
    })
    .catch(err => {
      questionText.textContent = 'Gagal memuat soal.';
      questionDomain.textContent = 'Belum diklasifikasikan';
      showToast('Tidak dapat terhubung ke server. Coba lagi.', 'error');
      console.error('startQuiz error:', err);
    });
}

function startTimer() {
  if (quizState.interval) clearInterval(quizState.interval);
  quizState.interval = setInterval(() => {
    quizState.timer -= 1;
    updateTimer();

    if (quizState.timer <= 60) {
      window.audioSystem?.startTicking();
    } else {
      window.audioSystem?.stopTicking();
    }

    if (quizState.timer <= 0) {
      clearInterval(quizState.interval);
      window.audioSystem?.stopTicking();
      saveResult();
    }
  }, 1000);
}

function updateTimer() {
  const minutes = String(Math.floor(quizState.timer / 60)).padStart(2, '0');
  const seconds = String(quizState.timer % 60).padStart(2, '0');
  timerElement.textContent = `${minutes}:${seconds}`;
}

function renderQuestion(question) {
  quizState.currentQuestion = question;
  quizState.index = question.currentIndex - 1;
  currentIndex.textContent = question.currentIndex;
  questionText.textContent = question.question;
  questionDomain.textContent = question.domain || 'Belum diklasifikasikan';
  scoreValue.textContent = quizState.score;
  progressBar.style.width = `${(question.currentIndex - 1) / question.total * 100}%`;

  const options = Object.entries(question.options).sort(() => Math.random() - 0.5);
  optionButtons.innerHTML = '';
  options.forEach(([key, value]) => {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'list-group-item list-group-item-action';
    button.textContent = `${key}. ${value}`;
    button.dataset.answer = key;
    button.addEventListener('click', () => chooseAnswer(button));
    optionButtons.appendChild(button);
  });
}

function chooseAnswer(button) {
  if (quizState.locked) return;
  quizState.locked = true;
  Array.from(optionButtons.children).forEach(btn => btn.disabled = true);
  const answer = button.dataset.answer;
  submitAnswer(answer);
}

function submitAnswer(answer) {
  fetch('../process/submit_answer.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ action: 'answer', answer }),
  })
    .then(res => res.json().then(data => ({ status: res.status, body: data })))
    .then(({ status, body }) => {
      if (status !== 200 || body.error) {
        const message = body.error || 'Terjadi kesalahan saat memproses jawaban.';
        showToast(message, 'error');
        return;
      }
      if (body.correct) {
        quizState.score = body.score;
        quizState.correct += 1;
        playTone('success');
        if (typeof confetti !== 'undefined') {
          confetti({ particleCount: 18, spread: 60, origin: { y: 0.6 } });
        }
        showToast('Benar!', 'success');
      } else {
        quizState.wrong += 1;
        playTone('error');
        showToast('Salah!', 'error');
      }
      scoreValue.textContent = body.score || quizState.score;
      if (body.done) {
        clearInterval(quizState.interval);
        window.audioSystem?.stopTicking();
        if (typeof confetti !== 'undefined') {
          confetti({ particleCount: 120, spread: 90, origin: { y: 0.4 } });
        }
        Swal.fire({
          title: 'Kuis selesai!',
          text: 'Skor Anda akan disimpan dan ditampilkan di halaman hasil.',
          icon: 'success',
          confirmButtonText: 'Lihat Hasil',
          background: '#0b1221',
          color: '#f8fafc',
          customClass: {
            confirmButton: 'btn btn-primary'
          },
          showClass: {
            popup: 'animate__animated animate__zoomIn'
          },
          hideClass: {
            popup: 'animate__animated animate__fadeOut'
          }
        }).then(() => saveResult());
        return;
      }
      setTimeout(() => {
        quizState.locked = false;
        renderQuestion(body.question);
      }, 700);
    })
    .catch(err => {
      showToast('Tidak dapat terhubung ke server. Coba lagi.', 'error');
      console.error('submitAnswer error:', err);
    });
}

function saveResult() {
  const completionTime = `${7 - Math.floor(quizState.timer / 60)}:${String(quizState.timer % 60).padStart(2, '0')}`;
  fetch('../process/save_score.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({
      score: quizState.score,
      correct_answers: quizState.correct,
      wrong_answers: quizState.wrong,
      completion_time: completionTime,
    }),
  })
    .then(res => res.json().then(data => ({ status: res.status, body: data })))
    .then(({ status, body }) => {
      if (status !== 200 || body.error) {
        const message = body.error || 'Terjadi kesalahan saat menyimpan skor.';
        showToast(message, 'error');
        return;
      }
      window.location.href = 'result.php';
    })
    .catch(() => {
      showToast('Tidak dapat terhubung ke server. Coba lagi.', 'error');
    });
}

function playTone(type) {
  if (type === 'success') {
    window.audioSystem?.playCorrect();
    return;
  }
  window.audioSystem?.playWrong();
}

if (restartButton) {
  restartButton.addEventListener('click', () => {
    startQuiz();
  });
}

startQuiz();
