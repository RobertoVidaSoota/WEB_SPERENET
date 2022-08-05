# API DE E-COMMERCE PARA COMPUTADORES

```php
    // PEGAR NOVOS PRODUTOS
    public function getNewProducts()
    {
        $products = DB::select("SELECT produto.*, format(avg(estrelas), 1) as media_estrelas,
        count(comentarios.fk_id_produto) as quantidade_comentarios
        FROM produto 
        JOIN comentarios
        ON produto.id = comentarios.fk_id_produto
        GROUP BY produto.id, nome_produto, link_imagem, preco_produto,
        descricao, created_at, updated_at
        ORDER BY produto.created_at desc
        LIMIT 8;");
        
        if($products)
        {
            return response()->json([
               "msg" => "Deu certo",
               "data" => $products
            ]);
        }else
        {
            return response()->json([
                "msg" => "Deu errado" 
             ]);
        }
    }
```


Esse projeto back-end irá fornecer uma trnsferência de dados com um aplicativo movel de um e-commerce para computadores e periféricos, garantindo o uso funcional do mesmo como autenticação e pesquisa de produtos.

Esse projeto servirá para minha lista de projetos que estou montanto com o
intuito de alavancar minha carreira em TI.

## Padrões MVC e REST

Foi ultilizado um padrão de projeto MVC para a construção do sistema, pois é um dos mais usados nos frameworks.

O padrão REST é o padrão para construção da API pois dar facilidade de documentação.

### Exemplos

- [Models](https://github.com/RobertoVidaSoota/WEB_SPERENET/tree/master/app/Models)
- [Views (rotas que se comunicão com as views)](https://github.com/RobertoVidaSoota/WEB_SPERENET/tree/master/routes)
- [Controllers](https://github.com/RobertoVidaSoota/WEB_SPERENET/tree/master/app/Http/Controllers)

## Tecnologias:

- Laravel
- PHP
- PagSeguro sdk
- google SMTP

Se quiser conhecer minhas habilidades entre no meu [linkedin](https://www.linkedin.com/in/roberto-carlos-677851174/).