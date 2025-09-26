<!-- primeiro painel -->
<div class="painel-div">
  <div class="painel">
    <h1 class="info">Bem-vindo à Lanchonete Do Burgas!</h1>
    <p class="info">A melhor lanchonete da cidade, servindo deliciosos hambúrgueres, batatas fritas e muito mais.</p> 
  </div>
</div>


<style>
  
/*  */
.painel-div {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.painel{
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background-image: url('public/hamburg3.png');
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
    aspect-ratio: 16/9;
    min-height: 600px;
    max-height: 600px;
    margin-top: 30px;
}

.painel h1 {
    font-size: 3.0em;
    margin-bottom: 20px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
}

.painelp {
    font-size: 1.0em;
    margin-bottom: 30px;
    text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
}

@media (max-width: 768px) {
    .painel {
        aspect-ratio: 9/16;
        min-height: 300px;
    }
}

.info {
    color: #ffffff;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7); 
}


</style>