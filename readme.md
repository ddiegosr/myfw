![Logo](logo.png)

## Introdução

O MyFw é um micro framework simples com *features* básicas como sistema de rotas facilitado, uma template engine simples que ajuda na criação de views e que futuramente contará com uma ORM para facilitar a manipulação de Banco de Dados.



## Rotas

O sistema de rotas do MyFw é bastante simples e muito semelhante ao de frameworks conhecidos como [Slim](https://www.slimframework.com) e [Laravel](https://laravel.com). Todas as rotas são concentradas em um unico arquivo chamado *routes.php* presente na pasta *app/* do sistema.

Na versão atual do MyFw temos quatro métodos para manipulação de rotas, são eles:

```php
$app->get();
$app->post();
$app->put();
$app->delete();
```

Como você ja deve ter notado o nome do método tem referência com o tipo da rota, se ela é **GET**, **POST**, **PUT** ou **DELETE**. Esses métodos recebem dois parâmetros. O primeiro deles é uma *string* contendo a rota a ser registrada, já o segundo pode ser uma *função anônima* do PHP ou uma *string* com o seguinte padrão *'Controller@action'* que serão executados quando a rota for acessada. Por exemplo:

```php
$app->get('/', 'HomeController@index');
```

Nesse código estamos registrando a rota **/** sendo do tipo **GET** que ao ser acessada irá executar o método **index** do **HomeController**. *Obs: todos os controllers ficam dentro de app/controllers.*

### Rotas com parâmetros

O sistema de rotas do MyFw permite que você passe parâmetros para as rotas da seguinte forma:

```php
$app->get('/posts/{id}', 'PostsController@show');
```

Dessa forma ao acessar a rota `http://localhost/posts/12` a *12* será passado ao método do controller como um parâmetro que será executado normalmente. Abaixo temos a mesma rota funcionando com uma função anônima.

```php
$app->get('/posts/{id}', function($id) {
  echo "Exibirá o post de id: $id";
});
```

*O funcionamento é mesmo para rotas do tipo **POST**, **PUT**, **DELETE**.*
