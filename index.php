<?php

/**
 * 
 * @author iberlot <@> ivanberlot@gmail.com
 * @todo FechaC 10/11/2016 - Lenguaje PHP 
 * 
 * @todo 
 * @version 0.1	- Version de inicio 
 * @package CMS
 * @category 
 */

/*
 * Querido programador:
 *
 * Cuando escribi este codigo, solo Dios y yo sabiamos como funcionaba.
 * Ahora, Solo Dios lo sabe!!!
 *
 * Asi que, si esta tratando de 'optimizar' esta rutina y fracasa (seguramente),
 * por favor, incremente el siguiente contador como una advertencia para el
 * siguiente colega:
 *
 * totalHorasPerdidasAqui = 1
 *
 */




?>






<!DOCTYPE html>
<html>
  
  <head>
  
  
<script>
(function(window,funcion){
    var teclasHEX = '';
    window.document.onkeydown = function(evento) {
        var tecla = evento ? evento.keyCode : window.event.keyCode;
        teclasHEX = (teclasHEX+tecla.toString(16)).substr(-20);
        if(teclasHEX=='26262828252725274241') funcion();
    }
})(window,function(){
    // Código que debe ejecutarse
    alert('Gracias por contar con nosotros! Atte. iberlot <@> ivanberlot@gmail.com =) =) =)');
});
</script>
  
  
  
    <script type="text/javascript">
      // This function is called on page load.


      function canvasSpaceGame() {

        // Get the canvas element.
        canvas = document.getElementById("myCanvas");

        // Make sure you got it.
        if (canvas.getContext)

        // If you have it, create a canvas user interface element.
        {
          // Specify 2d canvas type.
          ctx = canvas.getContext("2d");

          // Paint it black.
          ctx.fillStyle = "black";
          ctx.rect(0, 0, 300, 300);
          ctx.fill();

          // Paint the starfield.
          stars();

          // Draw space ship.
          makeShip();
        }
      }

      // Paint a random starfield.


      function stars() {

        // Draw 50 stars.
        for (i = 0; i <= 50; i++) {
          // Get random positions for stars.
          var x = Math.floor(Math.random() * 299)
          var y = Math.floor(Math.random() * 299)

          // Make the stars white
          ctx.fillStyle = "white";

          // Give the ship some room.
          if (x < 30 || y < 30) ctx.fillStyle = "black";

          // Draw an individual star.
          ctx.beginPath();
          ctx.arc(x, y, 3, 0, Math.PI * 2, true);
          ctx.closePath();
          ctx.fill();
        }
      }

      function makeShip() {

        // Draw saucer bottom.
        ctx.beginPath();
        ctx.moveTo(28.4, 16.9);
        ctx.bezierCurveTo(28.4, 19.7, 22.9, 22.0, 16.0, 22.0);
        ctx.bezierCurveTo(9.1, 22.0, 3.6, 19.7, 3.6, 16.9);
        ctx.bezierCurveTo(3.6, 14.1, 9.1, 11.8, 16.0, 11.8);
        ctx.bezierCurveTo(22.9, 11.8, 28.4, 14.1, 28.4, 16.9);
        ctx.closePath();
        ctx.fillStyle = "rgb(222, 103, 0)";
        ctx.fill();

        // Draw saucer top.
        ctx.beginPath();
        ctx.moveTo(22.3, 12.0);
        ctx.bezierCurveTo(22.3, 13.3, 19.4, 14.3, 15.9, 14.3);
        ctx.bezierCurveTo(12.4, 14.3, 9.6, 13.3, 9.6, 12.0);
        ctx.bezierCurveTo(9.6, 10.8, 12.4, 9.7, 15.9, 9.7);
        ctx.bezierCurveTo(19.4, 9.7, 22.3, 10.8, 22.3, 12.0);
        ctx.closePath();
        ctx.fillStyle = "rgb(51, 190, 0)";
        ctx.fill();
      }
    </script>
  </head>
  
  <body onload="canvasSpaceGame()">
    <h1>
      Canvas Space Game
    </h1>
    <canvas id="myCanvas" width="300" height="300">
    </canvas>
  </body>

</html>