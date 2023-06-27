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
