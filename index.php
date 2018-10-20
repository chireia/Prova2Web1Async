<?php
    $current = "Home";
    session_start();
    require_once "head.php";
    require_once "connectDB.php";

    /* Se o login não estiver realizado, manda para tela de login */
    $_SESSION['status']=='sucess'? true : header("location:login.php");
?>

    <main class="<?=$current?>">
        <header id="pc">
            <figure>
                <a href="index.php"><img src="img/netflix-text.png" alt="NextFlix"></a>
            </figure>

            <nav id="menus">
                <a href="index.php" <?=$current=='Home' ? "class='mhere'" : false?>><h3>Home</h3></a>
                <?=isset($_SESSION['type']) && $_SESSION['type']=='Admin'?"<a href='create.php'><h3>Create</h3></a>":false;?>
            </nav>

            <section id="user">
                <div>Eae 
                    <?= $_SESSION['user'] ?>
                </div>
                <div>
                    <?= "Você é um usuário ".$_SESSION['type'] ?>
                </div>
            </section>

            <button>
                <a href="login.php">Logoff</a>
            </button>
        </header>

        <header id="mobile">
            <figure>
                <a href="index.php"><img src="img/netflix-text.png" alt="NextFlix"></a>
            </figure>
            <div id="burger">
                <div id="burger-btn">
                    <i class="material-icons md-light">menu</i>
                </div>
                <nav id="burger-itens">
                    <div class="user">
                    <i class="material-icons">person</i> 
                        <span><?= $_SESSION['user'] ?></span>
                    </div>
                    <a href="index.php" <?=$current=='Home' ? "class='mhere'": false?>><i class="material-icons md-light <?=$current=='Home' ? 'mhere': false?>">home</i><span class="item <?=$current=='Home' ? 'mhere' : false?>">Home</span></a>
                    <?=isset($_SESSION['type']) && $_SESSION['type']=='Admin'?
                    "<a href='create.php'><i class='material-icons md-light'>backup</i><span class='item'>Create</span></a>":false;?>
                    <a href="login.php" class="logoff"><i class="material-icons md-light logoff">exit_to_app</i><span class="item logoff">Logoff</span></a>
                </nav>
            </div>
        </header>
        
        <section>
            <header>
                <h1>Movie List</h1>
                <i class="material-icons md-light refresh">refresh</i>
            </header>
            <?=(isset($_SESSION['delete']) && $_SESSION['delete']=='sucess')?"<span class='sucess'>Delete Sucess!</span>":false?>
            <?=(isset($_SESSION['delete']) && $_SESSION['delete']=='fail')?"<span class='fail'>Delete Fail!</span>":false?>

            <span class='fail' style="display: none;">Update Fail!</span>
            <span class='sucess' style="display: none;" >Update Sucess!</span>

        <article></article>
        </section>

        
        <footer>
            &copyhireia
        </footer>
    </main>
    <div id="popup" style="display: none"></div>

    <script>
        /* Confirmar delete */
        function sure(event) {
            var opt = confirm("Sure?");
            if (opt == true) {
            }
            else{
                event.preventDefault();
            }
        }

        /* Carrega a listagem de forma Async */
        $( function () {
            $("article").load("api.php", {action:"read"});
        })

        /* Botão para dar refresh caso necessário */
        $(".refresh").click(function () {
            $("article").load("api.php", {action:"read"});
        })

        /* Quando clicado, recupera os dados de forma async e mostra o popup de edição */
        $(document).on('click', '.edit', function (event) {
            $("#popup").load("api.php?", {action:"update_r", id:event.target.id});
            $("#popup").show();
            $("#popup").addClass("apear");
            setTimeout(() => {
                /* Pega todos os inputs e coloca num Array = fields */
                var fields = document.querySelectorAll('input, select, textarea');
                
                /* Adiciona no evento "focusout", a função check */
                for(let field of fields){
                    field.addEventListener("focusout", function(){ check(this); })    
                }
            }, 100);
        })

        /* botão para fechar o popup */
        $(document).on('click', '.close', function(){
            $("#popup").toggleClass("apear");
            setTimeout(() => {
                $("#popup").hide();
            }, 1000);
        })
        
        /* botão para salvar */
        $(document).on('click', '.save', function () {
            var formElement = document.querySelector("#form");                   //Seleciona o form. OBS não tenho ideia por que $("#form") não funciona.
            var formData = new FormData(formElement);                            //Pega todos os campos do form e joga dentro do objeto formData.
            var fields = document.querySelectorAll('input, select, textarea');
            var val = 0;
            for (let field of fields) {
                if(field.value == ""){
                    check(field);
                }else{
                    val++;
                }
            }
            if (val==fields.length) {
                $.ajax({
                        type: "POST",
                        url: "crud.php",
                        data: formData,
                        success: function (data) {                                      //Caso dê tudo certo
                            $( ".refresh" ).trigger( "click" );                         //Refresha os itens async
                            $("#popup").toggleClass("apear");
                            setTimeout(() => {
                                $("#popup").hide();
                            }, 1000);
                            if(data == "true"){                                         
                                $(".sucess").show();                                    //Notifica caso deu update
                            }else{
                                $(".fail").show();                                      //Notifica caso não deu update
                            }
                            setTimeout(() => {
                                $(".sucess").hide();
                                $(".fail").hide();
                            }, 2500);
                        },
                        processData: false,
                        contentType: false 
                });                
            }
            
        })
        
        /* Checa se os inputs estão vazios, caso estejam mostra a mesagem de "Campo obrigatório" */
        function check(x){
            if(x.value == ""){
                x.style.borderColor = "#F00";
            }
            else
            {
                x.style.borderColor = "#0F0";
            }
        }

    </script>
<?php
    unset($_SESSION['update']);
    unset($_SESSION['delete']);
    require_once "footer.php"
?>