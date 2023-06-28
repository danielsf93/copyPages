# copyPages para OMP
projeto para separar publicações por copyright<br>
por enquanto é uma adaptação do plugin staticPages<br>
<br>
está funcionando, salvando e exibindo as páginas estáticas:<br:<br>
![image](https://github.com/danielsf93/copyPages/assets/114300053/75cf68c9-bb03-4659-a746-d42e5d6e9425) <br><br>
# Problemas:
Foi necessário copiar duas tabelas do banco de dados. Copiei manualmente pelo php my admin:<br><br>
------------------------------------------------------------------------------------------------------------------------------------------<br>
static_pages         = copy_pages (mudando a coluna static_page_id para copy_page_id)<br>
------------------------------------------------------------------------------------------------------------------------------------------<br>
static_page_settings = copy_page_settings (mudando a coluna static_page_id para copy_page_id)<br>
------------------------------------------------------------------------------------------------------------------------------------------<br>



# anotações
-Se tentar utilizar as tabelas de static_pages, o arquivo content.tpl retorna  o original, ficando inviável o novo template.<br><br>


-Existe uma forma  de pegar os copyrights já  existentes dentro do phpmyadmin via sql.:<br>

SELECT LEAD(`setting_value`) OVER (ORDER BY `publication_id`) AS next_setting_value
FROM `ompccc`.`publication_settings`
WHERE CONVERT(`setting_name` USING utf8) = 'copyrightHolder'

<br><br>
pensar em uma forma via php pegar essas informações existentes e criar os links automaticamente, pois são mais de 60 copyrights da s do portal da ABCD.

<br><br>
Analista corrigiu o código para: SELECT DISTINCT setting_value FROM publication_settings WHERE setting_name = 'copyrightHolder';
<br>isso retornou 91 copyrights distintos, incluindo nomes pessoais, erros de escrita, mosod de escrita, etc. = inviável.

