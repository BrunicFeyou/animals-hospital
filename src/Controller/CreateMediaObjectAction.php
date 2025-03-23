<?php
 
namespace App\Controller;
 
use App\Entity\Media;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
 
#[AsController]
final class CreateMediaObjectAction extends AbstractController
{
    private Security $security;
    public function __invoke(Request $request, EntityManagerInterface $em, Security $security): JsonResponse
    {
        $this->security = $security;

        $currentUser = $this->security->getUser();

        if(!$currentUser){
            throw new Exception('Only connected users can add and update medias.');
        }

        if(!in_array('ROLE_ASSISTANT',$currentUser->getRoles()) && !in_array('ROLE_VETERINARIAN',$currentUser->getRoles())){
            throw new Exception('Only assistants and veterinarians can add and update medias.');
        }

        $uploadedFile = $request->files->get('file');
 
        // Rend le champ obligatoire
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }
 
        $media = new Media();
        $media->file = $uploadedFile;
 
        $em->persist($media);
        $em->flush();
 
        return new JsonResponse([
            'status' => 'success',
            'media' => [
              'id' => $media->getId(),
            ]
        ], 201);
    }
}