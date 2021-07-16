<?php
namespace PyAngelo\Repositories;

interface QuestionRepository {

  public function getLatestQuestions($offset, $limit);

  public function getLatestComments($offset, $limit);

  public function getUnansweredQuestions();

  public function getQuestionsByPersonId($personId);

  public function getQuestionById($questionId);

  public function getQuestionBySlug($slug);

  public function getQuestionBySlugWithStatus($slug, $personId);

  public function getPublishedQuestionComments($questionId);

  public function getNextQuestion($dateUpdated);

  public function getPreviousQuestion($dateUpdated);

  public function getCategoryBySlug($slug);

  public function getCategoryQuestionsBySlug($slug);

  public function insertQuestionComment($commentData);

  public function updateQuestionLastUpdatedDate($questionId);

  public function unpublishCommentById($commentId);

  public function createQuestion(
    $personId,
    $questionTitle,
    $question,
    $slug
  );

  public function answerQuestion(
    $questionId,
    $questionTitle,
    $question,
    $answer,
    $questionTypeId,
    $teacherId,
    $slug,
    $answeredAt
  );

  public function deleteQuestion($slug);

  public function getAllQuestionTypes();

  public function addToQuestionAlert($questionId, $personId);

  public function removeFromQuestionAlert($questionId, $personId);

  public function shouldUserReceiveAlert($questionId, $personId);

  public function getFollowers($questionId);

  public function getQuestionFavourited($questionId, $personId);

  public function addToQuestionFavourited($questionId, $personId);

  public function removeFromQuestionFavourited($questionId, $personId);

  public function getFavouriteQuestionsByPersonId($personId);
}
?>
