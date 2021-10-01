# TodoList - Authentication

This file will help you to understand how the authentication works in this application.

## Installation 

There is a bundle called security-bundle provided by Symfony that helps us to secure our application by managing the users with different roles and access control.
We install this bundle in our application with composer : 

```php 
composer require symfony/security-bundle
```

## User class 

The next step is to create our User class which will describe our user (username, password, etc.). 
We can do this easily with the MakerBundle :
 
```php
php bin/console make:user
```

Follow the step to add any fields you want to the user entity. Make sure your User class implements the UserInterface. 
It’s very important because it helps Symfony understand that you're managing users.

```php 
class User implements UserInterface {...}
```

Then, when you create or modify an entity like this don't forget to run a migration that will update the database. 

```php
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Security

With the SecurityBundle, Symfony creates a security.yaml **(config/packages)** file that will contain all our security configuration.
With the make:user command a lot of the configuration has been already done. Here I will help you understand some of the already done configuration 
and what you can add to make the application even more secure.

## User provider

User Providers are Symfony Security classes that  help you to reload the user from the session to make sure the user is not out-of-date. 
If the user is out-of-date the user provider refreshes it and if the user has changed it will de-authenticates it.  

```yaml
# config/packages/security.yaml
security:
    # ...
    providers:
        # used to reload user from session & other features (e.g. switch_user)
        app_user_provider:
            entity:
                class: App\Entity\User
                property: username
```

## Hashing Password

```yaml
# config/packages/security.yaml
security:
    # ...
    password_hashers:
        App\Entity\User:
            algorithm: auto
```

The hashing password configuration will allow you to control how the user’s password will be hashed. With the configuration algorithm: auto, 
Symfony will select the best way to encrypt the password in your application. Then when you creating a new user you can hash is password with 
the UserPasswordHasherInterface like this : 

```php
private $passwordHasher;

public function __construct(UserPasswordHasherInterface $passwordHasher)
{         
    $this->passwordHasher = $passwordHasher;
}

$user->setPassword($this->passwordHasher->hashPassword(
             $user,
             'the_new_password'
         ));
```

## Firewall

The firewall option is the more important configuration, it will describe how the user will be authenticated in the application. 
In our case, the user will use a form to login which will be located to the route /login. This is the route that the user will be redirected to when they try to access 
a protected resource but isn’t fully authenticated. The form login is implemented in the SecurityController :

```php
class SecurityController extends AbstractController 
{ 
  /** * @Route("/login", name="login") */ 
  public function login(AuthenticationUtils $authenticationUtils): Response { 

    if ($this->getUser()) {
         return $this->redirectToRoute('home'); 
    } 
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername(); 

    return $this->render('security/login.html.twig', ['last_username' => $lastUsername,  'error' => $error]  ); 
    } 
 }
```

Then we will check the user information with the login_check route. The firewall will intercept any requests to this URL and process the submitted login credentials
The user can log out by accessing the route logout.

```yaml
# config/packages/security.yaml
security:
    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            anonymous: true
            lazy: true
            provider: app_user_provider
            pattern: ^/
            form_login: 
              login_path: login 
              check_path: login_check 
```

## Authentication 

Symfony comes with a built-in **form_login** system that handles a login form POST automatically.
As said above, the user will be redirected to the login route when he tries to access a protected resource but isn’t fully authenticated.
When the user fills up the login form the firewall will intercept the request and process the authentication.

## Logging out 

```yaml
# config/packages/security.yaml
security:
    firewalls:
        main:
            logout:
                path:  logout
```

When the user will access the logout route, Symfony will intercept it and de-authenticated the user.

## Access Control

Users can now log in to our application using the login form. We can deny access and decide if a user can access any resource. 

When we create a user we can give him one or many roles. And then we have multiple choice to deny access to a resource to a certain role. 

1. In the security.yaml under the access_control configuration
```yaml
access_control:
        # require ROLE_ADMIN for /admin*
        - { path: '^/admin', roles: ROLE_ADMIN }
```

2. In a controller

    a. Inside a function

    ```php
    $this->denyAccessUnlessGranted('ROLE_ADMIN');
    ```
    b. With annotation

    ```php
    /**
    * @IsGranted("ROLE_ADMIN")
    */
    ```
  
3. In a twig template 
  ```twig
  {% if is_granted('ROLE_ADMIN') %}
    <a href="...">Delete</a>
  {% endif %}
  ````
