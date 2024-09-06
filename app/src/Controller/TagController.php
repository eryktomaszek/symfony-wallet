<?php
/**
 * This file is part of the Budgetly project.
 *
 * (c) Eryk Tomaszek 2024 <eryk.tomaszek@student.uj.edu.pl>
 */

namespace App\Controller;

use App\Entity\Tag;
use App\Form\Type\TagType;
use App\Service\TagServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class TagController.
 */
#[\Symfony\Component\Routing\Attribute\Route('/tag')]
#[IsGranted('ROLE_ADMIN')]
class TagController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param TagServiceInterface $tagService Tag service
     * @param TranslatorInterface $translator Translator service
     */
    public function __construct(private readonly TagServiceInterface $tagService, private readonly TranslatorInterface $translator)
    {
    }

    /**
     * Index action.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route(name: 'tag_index', methods: 'GET')]
    public function index(Request $request): Response
    {
        $pagination = $this->tagService->getPaginatedList($request->query->getInt('page', 1));

        return $this->render('tag/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show action.
     *
     * @param Tag $tag Tag entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}', name: 'tag_show', requirements: ['id' => '[1-9]\d*'], methods: 'GET')]
    #[ParamConverter('tag', class: \App\Entity\Tag::class)]
    public function show(Tag $tag): Response
    {
        return $this->render('tag/show.html.twig', ['tag' => $tag]);
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/create', name: 'tag_create', methods: 'GET|POST')]
    public function create(Request $request): Response
    {
        $tag = new Tag();
        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Tag     $tag     Tag entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/edit', name: 'tag_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[ParamConverter('tag', class: \App\Entity\Tag::class)]
    public function edit(Request $request, Tag $tag): Response
    {
        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->tagService->save($tag);

            $this->addFlash(
                'success',
                $this->translator->trans('message.updated_successfully')
            );

            return $this->redirectToRoute('tag_index');
        }

        return $this->render('tag/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Tag     $tag     Tag entity
     *
     * @return Response HTTP response
     */
    #[\Symfony\Component\Routing\Attribute\Route('/{id}/delete', name: 'tag_delete', requirements: ['id' => '[1-9]\d*'], methods: 'POST')]
    #[ParamConverter('tag', class: \App\Entity\Tag::class)]
    public function delete(Request $request, Tag $tag): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tag->getId(), $request->request->get('_token'))) {
            $this->tagService->delete($tag);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );
        }

        return $this->redirectToRoute('tag_index');
    }
}
