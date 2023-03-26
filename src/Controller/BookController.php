<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use App\Repository\DraftRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    public function index(BookRepository $bookRepository): Response
    {
        return $this->render('book/index.html.twig', [
            'books' => $bookRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    public function new(Request $request, BookRepository $bookRepository): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book, ['edit' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $bookRepository->save($book, true);

            return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('book/new.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Book $book, BookRepository $bookRepository): Response
    {
        $draft = $bookRepository->findDraft($book);

        if (null !== $draft) {
            $book = $draft;
        }

        $form = $this->createForm(BookType::class, $book, ['allow_extra_fields' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var SubmitButton $saveAsOriginal */
            $saveAsOriginal = $form->get('saveAsOriginal');
            /** @var SubmitButton $saveAsDraft */
            $saveAsDraft = $form->get('saveAsDraft');

            if ($form->has('resetToOriginal')) {
                /** @var SubmitButton $resetToOriginal */
                $resetToOriginal = $form->get('resetToOriginal');
                $resetToOriginalClicked = $resetToOriginal->isClicked();
            } else {
                $resetToOriginalClicked = false;
            }

            dump($book, $book->getOriginalBook());

            if ($saveAsOriginal->isClicked()) {
                if ($book->isDraft()) {
                    $originalBook = $book->getOriginalBook();
                    $book->copyTo($originalBook->setLastSaveDate(new DateTime()));

                    $bookRepository->save($originalBook, true);
                    $bookRepository->remove($book, true);
                    $book = $originalBook;
                } else {
                    $bookRepository->save($book);
                }
            } elseif ($saveAsDraft->isClicked()) {
                if (!$book->isDraft()) {
                    $draft = (new Book())
                        ->setIsDraft(true)
                        ->setOriginalBook($book);
                    $book->copyTo($draft);
                } else {
                    $draft = $book;
                }

                $bookRepository->save($draft, true);
                $book = $draft;
            } elseif ($resetToOriginalClicked && $book->isDraft()) {
                $originalBook = $book->getOriginalBook();
                //dd($book, $originalBook,$book->getBrief(), $originalBook->getBrief());
                $bookRepository->remove($book, true);
                $book = $originalBook;
            }

            $form = $this->createForm(BookType::class, $book, ['allow_extra_fields' => true]);
        }

        return $this->renderForm('book/edit.html.twig', [
            'book' => $book,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_book_delete', methods: ['POST'])]
    public function delete(Request $request, Book $book, BookRepository $bookRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $bookRepository->remove($book, true);
        }

        return $this->redirectToRoute('app_book_index', [], Response::HTTP_SEE_OTHER);
    }
}
