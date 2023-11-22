<?php

namespace App\Controller;

use Symfony\Component\Filesystem\Filesystem;
use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentFormType;
use App\Form\PostFormType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class BlogController extends AbstractController
{
    #[Route("/blog/buscar/{page}", name: 'blog_buscar')]
    public function buscar(ManagerRegistry $doctrine,  Request $request, int $page = 1): Response
    {
       $palabra = $request->query->get('searchTerm', ''); 
       $repositorio = $doctrine->getRepository(Post::class);
       $posts = $repositorio->findByTextPaginated($page, $palabra);
       $recentPosts = $repositorio->findRecents();
       return $this->render('blog/blog.html.twig', ['posts'=> $posts, 'recents'=>$recentPosts]);
    } 
   
    #[Route("/blog/new", name: 'new_post')]
    public function newPost(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger): Response
    {
        $post = new Post();
        $form = $this->createForm(PostFormType::class, $post);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('Image')->getData();
            if ($file) {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
        
                // Move the file to the directory where images are stored
                try {
        
                    $file->move(
                        $this->getParameter('images_directory'), $newFilename
                    );
                    $filesystem = new Filesystem();
                    $filesystem->copy(
                        $this->getParameter('images_directory') . '/'. $newFilename,
                        $this->getParameter('portfolio_directory') . '/'.  $newFilename, true);
        
                } catch (FileException $e) {
            
                }
                $post->setImage($newFilename);
            }  
            $repositorio = $doctrine->getRepository(Post::class);
            $slugExistente = $repositorio->findOneBy(['Slug' => $slugger->slug($post->getTitle())]);
            $post = $form->getData();
            $post->setUser($this->getUser());
            if($slugExistente) {
                $post->setSlug($slugger->slug($post->getTitle() . rand(0, 1000)));    
            } else {
                $post->setSlug($slugger->slug($post->getTitle()));
            }
            $post->setNumLikes(0);
            $post->setNumComments(0);
            $post->setNumViews(0);
            $entityManager = $doctrine->getManager();
            $entityManager->persist($post);
            $entityManager->flush();
            return $this->redirectToRoute('blog', []);
        }
        return $this->render('blog/new_post.html.twig', ['form' => $form->createView()]);
    }
    
    #[Route("/single_post/{slug}/like", name: 'post_like')]
    public function like(ManagerRegistry $doctrine, $slug): Response
    {   
        $repositorio = $doctrine->getRepository(Post::class);
        $post = $repositorio->findOneBy(['Slug' => $slug]);
        $post->setNumLikes($post->getNumLikes() + 1);
        $entityManager = $doctrine->getManager();
        $entityManager->persist($post);
        $entityManager->flush();
        return $this->redirectToRoute('single_post', ['slug'=> $slug]);
    }

    #[Route("/blog/{page}", name: 'blog')]
    public function index(ManagerRegistry $doctrine, int $page = 1): Response
    {
        $repository = $doctrine->getRepository(Post::class);
        $posts = $repository->findAllPaginated($page);
        $recentPosts = $repository->findRecents();
        
        return $this->render('blog/blog.html.twig', [
            'posts' => $posts, 'recents' => $recentPosts
        ]);
    }

    #[Route("/single_post/{slug}", name: 'single_post')]
    public function post(ManagerRegistry $doctrine, Request $request, $slug): Response
    {
        $repository = $doctrine->getRepository(Post::class);
        $post = $repository->findOneBy(["Slug"=>$slug]);
        $recentPosts = $repository->findRecents();
        $comment = new Comment();
        $form = $this->createForm(CommentFormType::class, $comment);
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $comment = $form->getData();
                $comment->setPost($post);  
                $post->setNumComments($post->getNumComments() + 1);
                $entityManager = $doctrine->getManager();    
                $entityManager->persist($comment);
                $entityManager->flush();
                return $this->redirectToRoute('single_post', ["slug" => $post->getSlug()]);
    }

        return $this->render('blog/single_post.html.twig', ['post' => $post, 'recents' => $recentPosts, 'commentForm'=>$form->createView()]);
    }
}
