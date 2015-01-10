<?php
namespace ABCMath\Meta\Implement;

interface ExamGenerator
{
    public function getRandomExamQuestions($keyword_id, $num = 10);
    public function getRandomExamSQL($keyword_id, $num = 10);
}
