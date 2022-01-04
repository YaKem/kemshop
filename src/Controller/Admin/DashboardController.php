<?php

namespace App\Controller\Admin;

use App\Entity\Cart;
use App\Entity\Order;
use App\Entity\Carrier;
use App\Entity\Contact;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\HomeSlider;
use App\Controller\Admin\OrderCrudController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(OrderCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Kemshop');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
        return parent::configureUserMenu($user)
            // use the given $user object to get the user name
            ->setName($user->getFirstname())
            ->setAvatarUrl("{{ asset('assets/images/logo_light.png') }}")
            // you can also pass an email address to use gravatar's service
            ->setGravatarEmail($user->getEmail())
            // you can use any type of menu item, except submenus
            ->addMenuItems([
                MenuItem::linkToRoute('Home', 'fa fa-home', 'home'),
            ]);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Products', 'fas fa-shopping-cart', Product::class);
        yield MenuItem::linkToCrud('Orders', 'fas fa-shopping-bag', Order::class);
        yield MenuItem::linkToCrud('Cart', 'fas fa-box', Cart::class);
        yield MenuItem::linkToCrud('Categories', 'fas fa-list', Category::class);
        yield MenuItem::linkToCrud('Carriers', 'fas fa-truck', Carrier::class);
        yield MenuItem::linkToCrud('Home Slider', 'fas fa-images', HomeSlider::class);
        yield MenuItem::linkToCrud('Contact', 'fas fa-envelope', Contact::class);
    }
}
