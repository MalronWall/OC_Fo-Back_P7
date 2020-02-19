Projet_7
========

# BileMo

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/327dbbd3ee3c4e71978503039b5aca65)](https://app.codacy.com/manual/MalronWall/OC_Fo-Back_P7?utm_source=github.com&utm_medium=referral&utm_content=MalronWall/OC_Fo-Back_P7&utm_campaign=Badge_Grade_Dashboard)

## Project installation

1.  Code recovery

    1. Git

        Connect with SSH key to your server.  
        Use the command : `git clone https://github.com/MalronWall/OC_Fo-Back_P7.git`

    1. FTP

        Download this [repository](https://github.com/MalronWall/OC_Fo-Back_P7/archive/master.zip).  
        Use a FTP client, for example [FileZilla](https://filezilla-project.org/) and connect to the server.  
        Use the FTP client to transfert the repository on your server.

1. Configuration

    Update environnements variables in the /app/config/parameters.yml file with your values.
    At the very least you need to define the SYMFONY_ENV=prod

1. Vendors installation

    1. Composer

        If you can use Composer in your server, use `composer install --no-dev -ao` for optimized installation of vendors.  
        If you can't use Composer, download [Composer.phar](https://getcomposer.org/download/) and use `php composer.phar install --no-dev -ao`.

    1. FTP

        If you can't use the both solutions, use your FTP client to download all vendors.  
        This solution is to be used only if no solution with Composer works.

1. Database creation

    Use the command `php bin/console d:d:c` for database creation.  
    Use the command `php bin/console d:m:m` for creation of the tables.
    Create the first Client manually in the database
    `[A valid UUID] | Admin | [An encoded password] | a:1:{i:0;s:10:"ROLE_ADMIN";}`


1. Documentation and API tests

    The documentation is on the uri `/api/doc`.
    If the uri is `/`, user redirected to `/api/doc`.  
    For the connection, use Postman (or other API Development Environment), and connect to the path `/api/login`, with credentials :
    ```json5
    {'username': 'Admin','password': '[the previously entered password in db]'}
    ```
    Copy the returned bearer token and connect by paste the token in the "Authorization" button then choose the Bearer Token security and enter your token.