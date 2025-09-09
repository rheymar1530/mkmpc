<html>

<!-- REFERENCE: https://w3hubs.com/css-text-portrait-effects/ -->
  <head>
    <title>LENI KIKO 2022</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
      body{
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        margin: 0px;
       background-color: #000000;
background-image: linear-gradient(315deg, #000000 0%, #414141 74%);
        /*margin-left: 200px;*/
      }
.center {
  margin: auto;
  width: 60%;
  /*border: 3px solid #73AD21;*/
  padding: 10px;
}
      div.center{
        font-size: 10px;
        line-height: 10px;
        background:url("{{URL::asset('dist/img/LENI_KIKO2.jpg')}}");
        background-repeat: no-repeat;
        background-size: auto 500px;

        /*width: 800px;*/
        /*background-size: 200px;*/
        /*width: 80%;*/
        background-position: center;
        -webkit-background-clip:text;
        background-attachment: fixed;
        -webkit-text-fill-color:rgba(115,115,115,0);
        word-break: break-all;
      }
    </style>
  </head>
  <body>
   <!--  <div>
      @for($i=0;$i<100;$i++)
        RASTAMAN YOW ! HALF HUMAN,HALF ZOMBIE. GABAY THIRD EYE, HAPPINY INFINITY. GHOST RIDER, MOTORCYCLE UMAAPOY MAY PAKPAK. WATCHUGANADO WITH THE BIG FAT BUT WIGUL WIGUL WIGUL. #LETRASTMANLEAD #RASTAMAN2022 #RASTAMANISMYPRESIDENT.
        @endfor

      </div> -->
  <div class="center">
      @for($i=0;$i<200;$i++)
      #LENIKIKO2022 #KULAYROSASANGBUKAS #LETLENILEAD
      @endfor
  </div>
  </body>




</html>