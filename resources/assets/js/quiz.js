const startButton = document.getElementById('startBtn');
const quizContainer = document.getElementById('quiz');
const hintContainer = document.getElementById('hint');
const feedbackContainer = document.getElementById('feedback');
const actionButton = document.getElementById('action');

const tutorialQuizId = quizContainer.getAttribute('data-tutorial-quiz-id');

const CHECK_ANSWER = 1;
const NEXT_QUESTION = 2;
const SHOW_SUMMARY = 3;
const DONE = 4;
let state = CHECK_ANSWER;

const MULTIPLE_CHOICE = 1;

let quizOptions = [];
let questionNo = 0;
let totalQuestions = 0;
let incorrectAttempts = 0;
let hintUsed = false;
let correctUnaidedTotal = 0;
let tutorialSlug;
let quizStartTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
let quizEndTime;
let questionStartTime;
let questionEndTime;
let skillsMatrix = []

function fetchQuestions(){
  startButton.style.display = "none";
  quizContainer.style.display = "block";
  feedbackContainer.style.display = "block";
  actionButton.style.display = "block";
  // Fetch the quizOptions
  fetch('/quizzes/questions/' + tutorialQuizId)
    .then(response => response.json())
    .then(data => {
      if (data.status !== 'success') {
        throw new Error(data.message);
      }
      quizOptions = data.options;
      tutorialSlug = data.tutorial_slug;
      totalQuestions = quizOptions.length;
      askQuestion(quizOptions[questionNo]);
    })
    .catch(error => { console.error(error); });
}

function askQuestion(currentQuestion) {
  questionStartTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
  if (currentQuestion.skill_question_type_id == 1) {
    askMultipleChoiceQuestion(currentQuestion);
  }
}

function askMultipleChoiceQuestion(currentQuestion) {
  const output = [];
  const answers = [];
  let correct = 0;

  for (option in currentQuestion.answers) {
      answers.push(
          `<div class="form-group">
            <label>
              <input type="radio" name="question${questionNo}" value="${currentQuestion.answers[option].skill_question_option_id}">
            ${currentQuestion.answers[option].option}
            </label>
          </div>`
      );
  }
  output.push(
      `<div class="question_number">Question ${questionNo + 1} of ${totalQuestions} </div>
       <div class="question">${currentQuestion.question}</div>
       <hr />
       <div class="answers">${answers.join('')}</div>`
  );
  quizContainer.innerHTML = output.join('');
  const radios = document.querySelectorAll('input[name="question' + questionNo + '"]');
  radios.forEach(radio => radio.addEventListener('change', () => actionButton.disabled=false));
}

function checkAnswer(currentQuestion) {
  if (currentQuestion.skill_question_type_id == 1) {
    checkMultipleChoiceAnswer(currentQuestion);
  }
}

function checkMultipleChoiceAnswer(currentQuestion) {
  let correctValue;
  for (option in currentQuestion.answers) {
      if (currentQuestion.answers[option].correct === 1) {
        correctValue = currentQuestion.answers[option].skill_question_option_id;
      }
  }
  const radioButtons = document.querySelectorAll('input[name="question' + questionNo + '"]');
  radioButtons.forEach(element => element.disabled=true);
  choice = document.querySelector('input[name="question' + questionNo + '"]:checked').value;

  let correctOrNot = 0;
  if (choice == correctValue) {
    if (incorrectAttempts == 0 && !hintUsed) {
      correctUnaidedTotal++;
      correctOrNot = 1;
    }
    questionEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
    recordResponse(
      currentQuestion.skill_question_id,
      choice,
      correctOrNot,
      questionStartTime,
      questionEndTime
    );

    feedbackContainer.innerHTML = `
      <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="alert-heading"><i class="fa fa-check"></i> Well Done!</h4>
        <p>Keep going!</p>
      </div>`;
    moveOn();
  }
  else {
    let heading = "Not quite yet...";
    incorrectAttempts++;
    if (incorrectAttempts == 2) {
      heading = "Still not correct, yet...";
    }
    else if (incorrectAttempts > 2) {
      heading = "Not yet. Keep persisting!";
    }
    feedbackContainer.innerHTML = `
      <div class="alert alert-info alert-dismissible" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="alert-heading"><i class="fa fa-repeat"></i> ${heading}</h4>
        <p>Try again, <a id="hintLink" href="#">get help</a>, or <a id="skipLink" href="#">skip for now</a>.</p>
      </div>`;
    if (currentQuestion.hints.length > 0) {
       const hintLink = document.getElementById('hintLink');
       hintLink.addEventListener('click', function(event) {
         event.preventDefault();
         hintUsed = true;
         const output = [];
         for (hint in currentQuestion.hints) {
           output.push(
             `<h4>Hint ${parseInt(hint)+1}</h4>
              <p>${currentQuestion.hints[hint].hint}</p>
              </div>`);
         }
         hintContainer.innerHTML = output.join('');
         hintContainer.style.display = "block";
       });
    }
    const skipLink = document.getElementById('skipLink');
    skipLink.addEventListener('click', function(event) {
      event.preventDefault();
      questionEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
      recordResponse(
        currentQuestion.skill_question_id,
        choice,
        correctOrNot,
        questionStartTime,
        questionEndTime
      );
      moveOn();
      processClick();
    });
    radioButtons.forEach(element => element.disabled=false);
  }
}

function moveOn() {
  // Next question or show summary
  hintContainer.style.display = "none";
  questionNo++;
  if (questionNo >= totalQuestions) {
    quizEndTime = new Date().toISOString().slice(0, 19).replace('T', ' ');
    recordQuizCompletion(quizStartTime, quizEndTime);
    state = SHOW_SUMMARY;
    actionButton.innerHTML = "Show Summary";
    actionButton.disabled=false;
  }
  else {
    state = NEXT_QUESTION;
    actionButton.innerHTML = "Next Question";
    incorrectAttempts = 0;
    hintUsed = false;
  }
}

function processClick() {
  actionButton.disabled=true;
  if (state == CHECK_ANSWER) {
    checkAnswer(quizOptions[questionNo]);
    actionButton.disabled=false;
  }
  else if (state == NEXT_QUESTION) {
    state = CHECK_ANSWER;
    actionButton.innerHTML = "Check Answer";
    feedbackContainer.innerHTML = "";
    askQuestion(quizOptions[questionNo]);
  }
  else if (state == SHOW_SUMMARY) {
    const output = [];
    actionButton.innerHTML = "Done";
    state = DONE;
    actionButton.disabled=false;
    feedbackContainer.innerHTML = "";
    output.push(`
      <h1>Keep going. Keep growing.</h1>
      <p>${correctUnaidedTotal}/${totalQuestions} correct!</p>`);
    output.push(
        `<div class="table-responsive skills-table">
          <table class="table table-striped table-hover">
            <tbody>`);
    skillsMatrix.forEach(function(skill) {
      if (skill.new_mastery_level_id > skill.mastery_level_id) {
        change = "<i class=\"fa fa-arrow-up\" aria-hidden=\"true\"></i>";
      }
      else if (skill.new_mastery_level_id == skill.mastery_level_id) {
        change = "<i class=\"fa fa-arrows-h\" aria-hidden=\"true\"></i>";
      }
      else {
        change = "<i class=\"fa fa-arrow-down\" aria-hidden=\"true\"></i>";
      }
      output.push(`
                <tr>
                  <td>${skill.skill_name}</td>
                  <td class="text-right">${skill.mastery_level_desc}</td>
                  <td class="text-right">${change}</td>
                  <td class="text-right">${skill.new_mastery_level_desc}</td>
                </tr>`);
    });
    output.push(
            `</tbody>
          </table>
        </div><!-- table-responsive -->`);
    quizContainer.innerHTML = output.join('');
  }
  else if (state == DONE) {
    window.location.href='/tutorials/' + tutorialSlug;
  }
}

function recordResponse(skillQuestionId, choice, correctOrNot, questionStartTime, questionEndTime) {
  const crsfToken = quizContainer.getAttribute('data-crsf-token');
  const data = "tutorialQuizId=" + encodeURIComponent(tutorialQuizId) + "&skillQuestionId=" + encodeURIComponent(skillQuestionId) + "&skillQuestionOptionId=" + encodeURIComponent(choice) + "&correctUnaided=" + encodeURIComponent(correctOrNot) + "&questionStartTime=" + encodeURIComponent(questionStartTime) + "&questionEndTime=" + encodeURIComponent(questionEndTime) +  "&crsfToken=" + encodeURIComponent(crsfToken);
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  };
  fetch('/quizzes/questions/record', options)
    .then(response => response.json())
    .then(data => { console.log(data); })
    .catch(error => { console.error(error); });
}

function recordQuizCompletion(quizStartTime, quizEndTime) {
  const crsfToken = quizContainer.getAttribute('data-crsf-token');
  const data = "tutorialQuizId=" + encodeURIComponent(tutorialQuizId) + "&quizStartTime=" + encodeURIComponent(quizStartTime) + "&quizEndTime=" + encodeURIComponent(quizEndTime) +  "&crsfToken=" + encodeURIComponent(crsfToken);
  const options = {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: data
  };
  fetch('/quizzes/questions/record-completion', options)
    .then(response => response.json())
    .then(data => {
      if (data.status !== 'success') {
        throw new Error(data.message);
      }
      skillsMatrix = data.skillsMatrix;
    })
    .catch(error => { console.error(error); });
}

// display quiz after start button pressed
startButton.addEventListener('click', fetchQuestions);

actionButton.disabled=true;
// on submit, check answer
actionButton.addEventListener('click', processClick);
