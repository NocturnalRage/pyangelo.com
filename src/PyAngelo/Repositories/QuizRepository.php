<?php
namespace PyAngelo\Repositories;

interface QuizRepository {

  public function getSkillBySlug($slug);

  public function getTutorialSkillsMastery($tutorialId, $personId);

  public function getSkillMastery($skillId, $personId);

  public function getAllSkillQuestions($skillId);

  public function getAllTutorialQuestions($tutorialId);

  public function createQuiz($quizTypeId, $keyId, $personId);

  public function addQuizQuestion($tutorialId, $skillQuestionId);

  public function getIncompleteSkillQuiz($skillId, $personId);

  public function getIncompleteSkillQuizInfo($skillId, $personId);

  public function getIncompleteTutorialQuiz($tutorialId, $personId);

  public function getIncompleteTutorialQuizInfo($tutorialId, $personId);

  public function getQuizOptions($quizId);

  public function updateQuiz(
    $quizId,
    $questionStartTime,
    $questionEndTime
  );

  public function updateQuizQuestion(
    $quizId,
    $skillQuestionId,
    $skillQuestionOptionId,
    $correctUnaided,
    $questionStartTime,
    $questionEndTime
  );

  public function getQuizResultsAndSkillMastery($quizId);

  public function getSkillQuestionHints($skillQuestionId);

  public function insertOrUpdateSkillMastery($skillId, $personId, $masteryLevelId);
}
