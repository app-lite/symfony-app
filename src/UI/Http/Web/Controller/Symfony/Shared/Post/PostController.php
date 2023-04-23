<?php

declare(strict_types=1);

namespace App\UI\Http\Web\Controller\Symfony\Shared\Post;

use App\Application\Command\Post\Post\PostCommand;
use App\Application\Command\Post\Post\PostHandler;
use App\Application\Query\Post\PostCategoryFetcher;
use App\Application\Query\Post\PostFetcher;
use App\Application\Validation\Post\PostValidation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolationInterface;

class PostController extends AbstractController
{
    public function index(
        PostCategoryFetcher $postCategoryFetcher,
        PostFetcher $postFetcher,
    ) {
        $postCategoryList = $postCategoryFetcher->getList();
        $postListGroupByCategoryId = $postFetcher->getByLimitGroupByCategoryId(3);

        $postCategoryList = array_replace($postListGroupByCategoryId, $postCategoryList);

        $postCount = $postFetcher->count();

        return $this->render('views/shared/post/post/index.html.twig',
            compact(
                'postCategoryList',
                'postListGroupByCategoryId',
                'postCount',
            )
        );
    }

    public function create(PostCategoryFetcher $postCategoryFetcher)
    {
        $postCategoryList = $postCategoryFetcher->getList();

        return $this->render('views/shared/post/post/create.html.twig', compact('postCategoryList'));
    }

    public function store(
        Request $request,
        PostValidation $validation,
        PostHandler $postHandler,
    ) {
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

            return $this->redirectToRoute('web.post.create');
        }
        $command = PostCommand::createFromData($data);
        $postHandler->handle($command);

        return $this->redirectToRoute('web.post.index');
    }
}
