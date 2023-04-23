<?php

declare(strict_types=1);

namespace App\UI\Http\Web\Controller\Symfony\Shared\Post;

use App\Application\Command\Post\PostCategory\PostCategoryCommand;
use App\Application\Command\Post\PostCategory\PostCategoryHandler;
use App\Application\Validation\Post\PostCategoryValidation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationInterface;

class PostCategoryController extends AbstractController
{
    public function create(): Response
    {
        return $this->render('views/shared/post/category/create.html.twig');
    }

    public function store(
        Request $request,
        PostCategoryValidation $validation,
        PostCategoryHandler $postCategoryHandler,
    ): RedirectResponse {
        $errors = [];
        if (!$this->isCsrfTokenValid('create', $request->request->get('token')) && 'test' !== $this->getParameter('app.env')) {
            $errors['token'][]['message'] = 'CSRF token missing or incorrect';
        }

        $data = $request->request->all();
        /** @var ConstraintViolationInterface[] $violations */
        $violations = $validation->validate($data);
        if (0 !== count($violations) || !empty($errors)) {
            foreach ($violations as $violation) {
                $propertyPath = str_replace(['[', ']'], ['', ''], $violation->getPropertyPath());
                $errors[$propertyPath][]['message'] = $violation->getMessage();
            }
            $this->addFlash('errors', $errors);
            $this->addFlash('old', $data);

            return $this->redirectToRoute('web.post.category.create');
        }
        $command = PostCategoryCommand::createFromData($data);
        $postCategoryHandler->handle($command);

        return $this->redirectToRoute('web.post.index');
    }
}
