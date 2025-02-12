<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuizController extends AbstractController
{
    #[Route('/', name: 'quiz')]
    public function index(QuizRepository $quizRepository): Response
    {
        $allQuizz = $quizRepository->findAll();
        return $this->render('quiz/index.html.twig', [
            'allQuizz' => $allQuizz,
        ]);
    }

    #[Route('/quiz/{quizId}/question/{questionIndex}', name: 'quiz_question')]
    public function showQuestion(int $quizId, int $questionIndex, Request $request, EntityManagerInterface $entityManager): Response
    {
        $quiz = $entityManager->getRepository(Quiz::class)->find($quizId);
    
        if (!$quiz) {
            throw $this->createNotFoundException('Quiz introuvable.');
        }
    
        $questions = $quiz->getQuestions();
        if (!isset($questions[$questionIndex])) {
            return $this->redirectToRoute('quiz_results', ['quizId' => $quizId]);
        }
    
        $question = $questions[$questionIndex];
        $isCorrect = null;
        $options = $question->getOptions();
    
        if ($request->isMethod('POST')) {
            $userAnswer = strtolower(trim($request->request->get('answer')));
            $correctAnswer = strtolower(trim($question->getAnswer()));
    
            $isCorrect = ($userAnswer === $correctAnswer);
        }
    
        return $this->render('quiz/question.html.twig', [
            'quiz' => $quiz,
            'question' => $question,
            'options' => $options,
            'questionIndex' => $questionIndex,
            'totalQuestions' => count($questions),
            'isCorrect' => $isCorrect,
        ]);
    }

    #[Route('/quiz/{quizId}/results', name: 'quiz_results')]
    public function quizResults(int $quizId, EntityManagerInterface $entityManager): Response
    {
        $quiz = $entityManager->getRepository(Quiz::class)->find($quizId);

        if (!$quiz) {
            throw $this->createNotFoundException('Quiz introuvable.');
        }

        return $this->render('quiz/results.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    
}
