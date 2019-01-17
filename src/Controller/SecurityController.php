<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\Security\ForgetPasswordType;
use App\Form\Security\LoginType;
use App\Form\Security\ResetPasswordType;
use App\Services\EmailService;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    /**
     * @var AuthenticationUtils
     */
    private $authenticationUtils;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * SecurityController constructor.
     * @param AuthenticationUtils $authenticationUtils
     * @param ValidatorInterface $validator
     */
    public function __construct(AuthenticationUtils $authenticationUtils, ValidatorInterface $validator)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->validator = $validator;
    }

    public function loginAction()
    {
        $user = new User();
        $user->setEmail($this->authenticationUtils->getLastUsername());

        $error = $this->authenticationUtils->getLastAuthenticationError();
        $form = $this->createForm(LoginType::class, $user, [
            'action' => $this->generateUrl('login_check')
        ]);

        return $this->render('base/sidebar/security/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }

    /**
     * @Route("/forget_password", name="security_forget_password")
     *
     * @throws \Exception
     */
    public function forgetPasswordAction(Request $request, EmailService $emailService)
    {
        $user = new User();
        $user->setEmail($this->authenticationUtils->getLastUsername());

        $form = $this->createForm(ForgetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && !count($this->validator->validate($form, null, ['forget_password']))) {
            $em = $this->getDoctrine()->getManager();

            /** @var User $user */
            $user = $em->getRepository(User::class)->findOneBy(['email' => $user->getEmail()]);
            if (!$user) {
                return $this->render('security/forget_password.html.twig', [
                    'form' => $form->createView(),
                    'message' => 'User not found'
                ]);
            }
            $user->setHash(Uuid::uuid4());
            $em->flush();

            $message = (new \Swift_Message('Reset password'))
                ->setFrom('support@symfoblog.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView('emails/forget_password.html.twig', ['user' => $user]),
                    'text/html'
                );
            $emailService->sendEmail($message);

            unset($user);
            unset($form);
            $user = new User();
            $form = $this->createForm(ForgetPasswordType::class, $user);

            return $this->render('security/forget_password.html.twig', [
                'form' => $form->createView(),
                'message' => 'Email with link to reset password has been successfully sent to your email!'
            ]);
        }

        return $this->render('security/forget_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/reset_password/{hash}", name="security_reset_password")
     *
     * @throws \Exception
     */
    public function resetPasswordAction(Request $request, string $hash)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['hash' => $hash]);

        if (!$user)
            throw new \Exception("Page not found");

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && !count($this->validator->validate($form, null, ['reset_password']))) {
            $user->setHash(Uuid::uuid4());
            $em->flush();

            return $this->redirectToRoute('default');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
