<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StayMate</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Merienda:wght@300..900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <style>
        *{
            font-family: "Poppins", sans-serif;
        }
        .h-font{
            font-family: "Merienda", cursive;

        }
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
      .swiper-container img {
        width: 100%;
        height: 50vh;     /* image takes only half of the screen height */
        object-fit: cover;
        }

        /* Medium laptops */
        @media (max-width: 1400px) {
        .swiper-container img {
        height: 50vh;
        }
        }

        /* Tablets */
        @media (max-width: 992px) {
        .swiper-container img {
        height: 40vh;
        }
        }

        /* Mobile */
        @media (max-width: 576px) {
        .swiper-container img {
        height: 30vh;
        }
        }
        .custom-bg{
            background-color: #2ec1ac;
        }
        .custom-bg:hover{
            background-color: #279e8c;
        }
        .availability-form{
            margin-top:-50px;
            z-index: 2;
            position: relative;
        }
         @media screen and (max-width: 576px) {
            .availability-form{
            margin-top:25px;
            padding: 0 35px;
            
            }

         }

    </style>
</head>