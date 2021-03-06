<?php

declare(strict_types=1);

namespace App\UI\Http\Web\Controller\Symfony\Shared\Post;

use App\Application\Command\Post\PostCategory\PostCategoryCommand;
use App\Application\Command\Post\PostCategory\PostCategoryHandler;
use App\Application\Validation\Post\PostCategoryValidation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationInterface;

class PostCategoryController extends AbstractController
{
    public function create()
    {
        return $this->render('views/shared/post/category/create.html.twig');
    }

    public function store(
        Request $request,
        PostCategoryValidation $validation,
        PostCategoryHandler $postCategoryHandler,
    ) {
        $errors = [];
        if (!$this->isCsrfTokenValid('create', $request->request->get('token')) && $this->getParameter('app.env') !== 'test') {
            $errors['token'][]['message'] = 'CSRF token missing or incorrect';
        }

        $data = $request->request->all();
        /** @var ConstraintViolationInterface[] $violations */
        $violations = $validation->validate($data);
        if (count($violations) !== 0 || !empty($errors)) {
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
