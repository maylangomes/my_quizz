<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class QuizController extends AbstractController
{
    #[Route('/quiz', name: 'app_quiz')]
    public function index(QuizRepository $quizRepository): Response
    {
        $allQuizz = $quizRepository->findAll();
        return $this->render('quiz/index.html.twig', [
            'allQuizz' => $allQuizz,
        ]);
    }

    #[Route('/quiz/{id}', name: 'show_quiz')]
    public function show(Quiz $quiz): Response
    {
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }
}
