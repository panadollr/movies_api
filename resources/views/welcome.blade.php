<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en"> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en"> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en"> <!--<![endif]-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="description" content="Name of your web site">
<meta name="author" content="Marketify">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<title class="avatar" data-img-url="https://res.cloudinary.com/ds7ojbei5/image/upload/v1717562486/438300345_966489821698443_13384136552625112_na_hlaiyk.jpg">Quang Minh Portfolio</title>
<link rel="icon" href="https://res.cloudinary.com/ds7ojbei5/image/upload/v1717562486/438300345_966489821698443_13384136552625112_na_hlaiyk.jpg">

<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Syne:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
<link rel="stylesheet" type="text/css" href="{{asset('css/plugins.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('css/modalboxes.css')}}" />
<link rel="stylesheet" type="text/css" href="{{asset('css/style.css')}}" />
<!--[if lt IE 9]> <script type="text/javascript" src="js/modernizr.custom.js"></script> <![endif]-->

</head>
<body>

<div id="preloader">
<div class="loader_line"></div>
</div>


<div class="arlo_tm_all_wrap" data-enter="fadeInLeft" data-exit>

<div class="arlo_tm_background_effects" data-style="canvas"></div>



<div class="arlo_tm_topbar">
<div class="topbar_inner">
<div class="logo" data-type="avatar"> 
<a href="google.com"><div class="avatar" data-img-url="https://res.cloudinary.com/ds7ojbei5/image/upload/v1717562486/438300345_966489821698443_13384136552625112_na_hlaiyk.jpg"></div></a>
<div class="image"><img src="img/logo/logo.png" alt /></div>
<div class="text"><h3>Portfolio</h3></div>
</div>
<div class="trigger">
<div class="hamburger hamburger--slider">
<div class="hamburger-box">
<div class="hamburger-inner"></div>
</div>
</div>
</div>
</div>
</div>
<div class="arlo_tm_mobile_menu">
<div class="inner">
<div class="wrapper">
<div class="logo" data-type="avatar"> 
<div class="avatar" data-img-url="img/about/1.jpg"></div>
<div class="image"><img src="img/logo/logo.png" alt /></div>
<div class="text"><h3>Portfolio</h3></div>
</div>
<div class="menu_list">
<ul class="transition_link">
<li class="active"><a href="#home">Home</a></li>
<li><a href="#home" class="avatar"></a></li>
<li><a href="#about">About</a></li>
<li><a href="#services">Projects</a></li>
<li><a href="#portfolio">Portfolio</a></li>
<li><a href="#blog">Blog</a></li>
<li><a href="#contact">Contact</a></li>
</ul>
</div>
<div  class="social">
<ul>
<li><a href="#">
    <img src="{{asset('img/svg/social/facebook.svg')}}" alt="" srcset="">
</a></li>
<li><a href="#home"><i class="avatar"></i></a></li>
<li><a href="#"><i class="icon-vimeo-squared"></i></a></li>
<li><a href="#"><i class="icon-youtube-squared"></i></a></li>
<li><a href="#"><i class="icon-skype-circled"></i></a></li>
</ul>
</div>
<div class="copyright">
<p>Copyright &copy; 2024</p>
</div>
</div>
</div>
</div>


<div class="arlo_tm_sidebar_menu">
<div class="sidebar_inner">
<div class="logo" data-type="avatar"> 
<ul class="transition_link"><a href="#home"><div class="avatar" data-img-url="{{$profile->avatar}}"></div></a></ul>
<div class="image"><img src="img/logo/logo.png" alt /></div>
<div class="text"><h3>Portfolio</h3></div>
</div>
<div class="menu" style="font-size: 20px;">
<ul class="transition_link">
<li class="active"><a href="#home">Home</a></li>
<li><a href="#about">About</a></li>
<li><a href="#services">Services</a></li>
<li><a href="#portfolio">Portfolio</a></li>
<li><a href="#blog">Blog</a></li>
<li><a href="#contact">Contact</a></li>
</ul>
</div>
<div class="copyright">
<p>Copyright &copy; 2024     <a class="line_effect" href="#"></a></p>
<p>All rights are reserved</p>
</div>
<div class="social">
<ul>
<li><a href="https://www.facebook.com/lqminh.0211">
<img src="{{asset('img/svg/social/facebook.svg')}}" alt="" srcset="">
</a></li>
</ul>
</div>
</div>
</div>


<div class="arlo_tm_mainpart">
<div class="mainpart_inner">

<div id="home" class="arlo_tm_section animated">
<div class="arlo_tm_home">
<div class="content">
<h3>{{$profile->name}}</h3>
<div class="animateText">
<span>Web Developer</span>
<span>UI/UX Designer</span>
<span>Game Developer</span>
<span>Graphic Designer</span>
</div>
</div>
</div>
</div>


<div id="about" class="arlo_tm_section">
<div class="section_inner">
<div class="arlo_tm_about">
<div class="biography">
<div class="arlo_tm_title">
<h3 style="background-color: #2e5abc; color: white; font-size: 16px;">About Me</h3>
</div>
<div class="text">
<p><span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">{{$profile->about}}</span></p>
</div>
<div class="details">
<ul>
<li>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">Name:</span>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">{{$profile->name}}</span>
</li>
<li>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">Age:</span>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">{{$profile->age}}</span>
</li>
<li>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">City:</span>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">{{$profile->city}}</span>
</li>
<li>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">Job:</span>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">{{$profile->job}}</span>
</li>
<li>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">Phone:</span>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;"><a class="line_effect" href="#">{{$profile->phone}}</a></span>
</li>
<li>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;">Email:</span>
<span style="color: black; font-size: 18px; font-family: Franklin Gothic Demi Cond;"><a class="line_effect" href="#"><span class="__cf_email__" data-cfemail="37524f565a475b5277505a565e5b1954585a">{{$profile->email}}</span></a></span>
</li>
</ul>
</div>
<div class="arlo_tm_button">
<a target="_blank" href="{{asset('CV.pdf')}}" downlaod>
<span class="back">Download CV</span>
<span style="background-color: black; font-size: 18px;" class="front">Download CV</span>
</a>
</div>
</div>
<div class="skillbox">
<div class="arlo_tm_title">
<h3 style="background-color: #2e5abc; color: white; font-size: 16px;">Knowledge</h3>
</div>
<div class="wrapper">
<div class="left">
<div class="skills_title">
<h3>Development Skills</h3>
</div>
<div class="arlo_progress">
<div class="progress_inner" data-value="85" data-color="#999">
<span><span class="label">PHP</span><span class="number">75%</span></span>
<div class="background"><div class="bar"><div class="bar_in"></div></div></div>
</div>
<div class="progress_inner" data-value="95" data-color="#999">
<span><span class="label">Unity</span><span class="number">90%</span></span>
<div class="background"><div class="bar"><div class="bar_in"></div></div></div>
</div>
<div class="progress_inner" data-value="75" data-color="#999">
<span><span class="label">C#</span><span class="number">85%</span></span>
<div class="background"><div class="bar"><div class="bar_in"></div></div></div>
</div>
<div class="progress_inner" data-value="85" data-color="#999">
<span><span class="label">C</span><span class="number">80%</span></span>
<div class="background"><div class="bar"><div class="bar_in"></div></div></div>
</div>
<div class="progress_inner" data-value="85" data-color="#999">
<span><span class="label">WordPress</span><span class="number">70%</span></span>
<div class="background"><div class="bar"><div class="bar_in"></div></div></div>
</div>
</div>
</div>

<div class="right">
<div class="skills_title">
<h3>Design Skills</h3>
</div>
<div class="arlo_progress">
<div class="progress_inner" data-value="75" data-color="#999">
<span><span class="label">Illustrator</span><span class="number">75%</span></span>
<div class="background"><div class="bar"><div class="bar_in"></div></div></div>
</div>
<div class="progress_inner" data-value="80" data-color="#999">
<span><span class="label">Photoshop</span><span class="number">80%</span></span>
<div class="background"><div class="bar"><div class="bar_in"></div></div></div>
</div>
<div class="progress_inner" data-value="70" data-color="#999">
<span><span class="label">After Effects</span><span class="number">70%</span></span>
<div class="background"><div class="bar"><div class="bar_in"></div></div></div>
</div>
<div class="progress_inner" data-value="80" data-color="#999">
<span><span class="label">Premiere</span><span class="number">80%</span></span>
<div class="background"><div class="bar"><div class="bar_in"></div></div></div>
</div>
<div class="progress_inner" data-value="65" data-color="#999">
<span><span class="label">3DsMax</span><span class="number">65%</span></span>
<div class="background"><div class="bar"><div class="bar_in"></div></div></div>
</div>
</div>
</div>
</div>
</div>
<div class="experience">
<div class="arlo_tm_title">
<h3 style="background-color: #2e5abc; color: white; font-size: 16px;">Timeline</h3>
</div>
<div class="wrapper">
<div class="left">
<div class="experience_title">
<h3>Working Experience</h3>
</div>
<div class="arlo_tm_experience_list">
<ul>
<li>
<div class="list_inner">
<div class="subject">
<h3>Internship</h3>
<span>Web Designer</span>
</div>
<div class="date">
<span>2020-2023</span>
</div>
</div>
</li>
<li>
<div class="list_inner">
<div class="subject">
<h3>Behance</h3>
<span>Designer</span>
</div>
<div class="date">
<span>2022-2023</span>
</div>
</div>
</li>
<li>
<div class="list_inner">
<div class="subject">
<h3>Unity</h3>
<span>Developer</span>
</div>
<div class="date">
<span>2023-Now</span>
</div>
</div>
</li>
</ul>
</div>
</div>
<div class="right">
<div class="experience_title">
<h3>Educational Experience</h3>
</div>
<div class="arlo_tm_experience_list">
<ul>
<li>
<div class="list_inner">
<div class="subject">
<h3>University of Information and Communication Technology</h3>
<span>Multimedia communications</span>
</div>
<div class="date">
<span>2020-2025</span>
</div>
</div>
</li>
<li>
<div class="list_inner">
<div class="subject">
<h3>Webster College</h3>
<span>UI/UX Design</span>
</div>
<div class="date">
<span>2020-2024</span>
</div>
</div>
</li>
<li>
<div class="list_inner">
<div class="subject">
<h3>Github Club</h3>
<span>Web Sertification</span>
</div>
<div class="date">
<span>2020-2023</span>
</div>
</div>
</li>
</ul>
</div>
</div>
</div>
</div>
<div class="arlo_tm_testimonials">
<div class="arlo_tm_title">
<h3 style="background-color: #2e5abc; color: white; font-size: 16px;">Testimonials</h3>
</div>
<div class="testimonials_inner">
<div class="quote">
<!-- //<img class="svg" src="img/svg/quote.svg" alt /> -->
</div>
<div class="wrapper owl-carousel">
<div class="item">
<div class="text">
<p>I rarely like to write reviews, but the Marketify team truly deserve a standing ovation for their customer support, customisation and most importantly is professionalism. Many thanks once again for everything and hope that I get to deal with you again in the near future!</p>
</div>
<div class="details">
<div class="avatar">
<div class="image" data-img-url="img/testimonials/1.jpg"></div>
</div>
<div class="info">
<h3 class="author"><span>Lan Nguyen</span></h3>
<h3 class="job"><span>Web Developer</span></h3>
</div>
</div>
</div>
<div class="item">
<div class="text">
<p>Minimal design, incredibly well documented, and an absolute pleasure to use! The customer support is one of the absolute best I've ever had the pleasure of interacting with. Quick, courteous, and extremely helpful! Thanks a lot for your hard work!</p>
</div>
<div class="details">
<div class="avatar">
<div class="image" data-img-url="img/testimonials/2.jpg"></div>
</div>
<div class="info">
<h3 class="author"><span>Hieu Dinh</span></h3>
<h3 class="job"><span>Photographer</span></h3>
</div>
</div>
</div>
<div class="item">
<div class="text">
<p>Loved the template design, documentation, customizability and the customer support from Marketify team! I am a noob in programming with very little knowledge about coding but the Marketify team helped me to launch my resume website successfully.</p>
</div>
<div class="details">
<div class="avatar">
<div class="image" data-img-url="img/testimonials/3.jpg"></div>
</div>
<div class="info">
<h3 class="author"><span>Duy Khanh</span></h3>
<h3 class="job"><span>Designer</span></h3>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
<div id="services" class="arlo_tm_section">
<div class="section_inner">
<div class="arlo_tm_services">
<div class="services_list">
<div class="arlo_tm_title">
<h3 style="background-color: #2e5abc; color: white; font-size: 16px;">Projects</h3>
</div>
<ul>
<li>
<div class="list_inner">
<svg xmlns="http://www.w3.org/2000/svg" height="400pt" viewBox="0 -59 400 400" width="400pt" class="svg replaced-svg"><path d="m329.5 30h-89.5v-20.199219c.101562-5.300781-4.199219-9.699219-9.5-9.800781-.101562 0-.199219 0-.300781 0h-61.199219c-5.398438-.0976562-9.898438 4.203125-10 9.703125v.097656 20.199219h-89.300781c-5.5 0-10 4.5-10 10s4.5 10 10 10h60.601562c-24.199219 21-39.300781 50.800781-39.300781 83.703125v67.296875h-18.199219c-5.402343 0-9.800781 4.402344-9.800781 9.800781v.199219 61.203125c-.101562 5.296875 4.199219 9.699219 9.5 9.796875h.300781 61.199219c5.398438.101562 9.898438-4.296875 10-9.699219v-.097656-61.203125c0-5.5-4.5-10-10-10h-23v-67.296875c.101562-33.101563 18.601562-63.300781 48-78.5v15.796875c0 5.5 4.398438 10 9.898438 10h61.300781c5.402343 0 9.800781-4.398438 9.800781-9.796875 0-.101563 0-.101563 0-.203125v-15.796875c29.398438 15.097656 48 45.398437 48 78.5v67.296875h-22.800781c-5.597657 0-10.097657 4.402344-10.199219 10v61.203125c.101562 5.5 4.699219 9.898437 10.199219 9.796875h61.199219c5.203124.101562 9.5-4.097656 9.601562-9.398438 0-.101562 0-.300781 0-.398437v-61.203125c.199219-5.296875-4-9.796875-9.300781-10-.097657 0-.199219 0-.300781 0h-18.398438v-67.296875c0-33-14.800781-62.703125-39-83.703125h60.5c5.5 0 10-4.5 10-10s-4.5-10-10-10zm-205.5 232h-41v-41h41zm192 0h-41v-41h41zm-137-201v-41h41v16.601562 6.898438 17.5zm0 0"></path><g><path d="m39.101562 80.300781c-21.5 0-39.101562-17.898437-39.101562-39.898437s17.5-39.902344 39.101562-39.902344c21.597657 0 39.097657 17.902344 39.097657 39.902344s-17.597657 39.898437-39.097657 39.898437zm0-59.800781c-10.5 0-19.101562 8.902344-19.101562 19.902344s8.601562 19.898437 19.101562 19.898437 19.097657-8.898437 19.097657-19.898437-8.597657-19.902344-19.097657-19.902344zm0 0"></path><path d="m360.101562 79.5c-22 0-39.902343-17.5-39.902343-39.097656 0-21.601563 17.902343-39.101563 39.902343-39.101563s39.898438 17.5 39.898438 39.101563c0 21.597656-17.898438 39.097656-39.898438 39.097656zm0-58.199219c-11 0-19.902343 8.601563-19.902343 19.101563s8.902343 19.097656 19.902343 19.097656 19.898438-8.597656 19.898438-19.097656-8.898438-19.101563-19.898438-19.101563zm0 0"></path></g></svg>
<h3 style="color: black; font-size: 18px;" class="title">Game Development</h3>
<div class="list">
<ul>
<li style="font-size: 16px; font-family: Berlin Sans FB Demi Bold"><span>C# Laguage</span></li>
<li><span>Unity Software</span></li>
<li style="font-size: 16px; font-family: Berlin Sans FB Demi Bold"><span>2D 3D Design</span></li>
</ul>
</div>
</div>
</li>
<li>
<div class="list_inner">
<svg xmlns="http://www.w3.org/2000/svg" height="400pt" viewBox="0 -22 400 400" width="400pt" class="svg replaced-svg"><path d="m50 256.007812v-246.015624c-22.089844 0-40 17.910156-40 40v256.015624c0 22.089844 17.910156 40 40 40h300c22.089844 0 40-17.910156 40-40v-10h-300c-22.089844 0-40-17.910156-40-40zm0 0" fill="none"></path><g><path d="m198.410156 99.574219c-17.421875 0-31.59375-14.398438-31.59375-32.105469s14.171875-32.105469 31.59375-32.105469c17.417969 0 31.589844 14.402344 31.589844 32.105469s-14.171875 32.105469-31.589844 32.105469zm0-44.210938c-6.6875-.292969-12.34375 4.890625-12.632812 11.578125-.292969 6.683594 4.890625 12.339844 11.574218 12.632813 6.6875.292969 12.34375-4.890625 12.636719-11.574219.007813-.175781.011719-.351562.011719-.527344.132812-6.542968-5.050781-11.957031-11.589844-12.109375zm0 0"></path><path d="m127.953125 99.574219c-17.417969 0-31.589844-14.398438-31.589844-32.105469s14.171875-32.105469 31.589844-32.105469 31.59375 14.398438 31.59375 32.105469-14.171875 32.105469-31.59375 32.105469zm0-44.210938c-6.683594-.292969-12.339844 4.890625-12.632813 11.578125-.289062 6.683594 4.894532 12.339844 11.578126 12.632813 6.6875.292969 12.34375-4.890625 12.636718-11.574219.007813-.175781.011719-.351562.011719-.527344.132813-6.542968-5.050781-11.957031-11.59375-12.109375zm0 0"></path><path d="m61.816406 99.574219c-17.417968 0-31.589844-14.398438-31.589844-32.105469s14.171876-32.105469 31.589844-32.105469c17.421875 0 31.59375 14.398438 31.59375 32.105469s-14.171875 32.105469-31.59375 32.105469zm0-44.210938c-6.683594-.289062-12.339844 4.894531-12.628906 11.578125-.292969 6.6875 4.890625 12.339844 11.578125 12.632813 6.683594.292969 12.339844-4.890625 12.632813-11.574219.007812-.175781.011718-.351562.011718-.527344.136719-6.542968-5.050781-11.960937-11.59375-12.109375zm0 0"></path></g><path d="m400 50c0-27.613281-22.386719-50-50-50h-300c-27.613281 0-50 22.386719-50 50v256c0 27.613281 22.386719 50 50 50h300c27.613281 0 50-22.386719 50-50zm-350-30h300c16.558594.015625 29.980469 13.433594 30 29.992188v74.007812h-360v-74.007812c.019531-16.558594 13.441406-29.976563 30-29.992188zm300 316h-300c-16.558594-.015625-29.980469-13.433594-30-29.992188v-162.007812h360v162.007812c-.019531 16.558594-13.441406 29.976563-30 29.992188zm0 0"></path></svg>
<h3 style="color: black; font-size: 18px;" class="title">Web Development</h3>
<div class="list">
<ul>
<li><span>HTML Websites</span></li>
<li><span>Wordpress Websites</span></li>
<li><span>CSS &amp; JavaScript &amp; Boostrap</span></li>
</ul>
</div>
</div>
</li>
<li>
<div class="list_inner">
<svg xmlns="http://www.w3.org/2000/svg" height="400pt" viewBox="-8 0 399 400" width="400pt" class="svg replaced-svg"><g fill="none"><path d="m118.726562 220c-50.855468-39.652344-78.054687-77.972656-64.980468-97.808594 12.769531-19.378906 60.679687-16.140625 120.972656 4.78125 9.929688-50.617187 27.714844-86.875 48.613281-95.167968-9.210937-13.921876-19.703125-21.804688-30.835937-21.804688-24.804688 0-46.421875 39.101562-57.777344 96.972656-60.292969-20.921875-108.199219-24.160156-120.972656-4.78125-13.074219 19.835938 14.125 58.15625 64.980468 97.808594-50.855468 39.652344-78.054687 77.972656-64.980468 97.808594 5.808594 8.820312 18.914062 12.9375 37.132812 12.855468-3.976562-21.085937 22.550782-55.347656 67.847656-90.664062zm0 0"></path><path d="m290.269531 126.972656c32.859375-11.398437 62.019531-17.535156 83.839844-17.636718-.46875-2.550782-1.441406-4.980469-2.863281-7.144532-10.355469-15.714844-43.816406-16.554687-88.25-5.15625 2.742187 9.257813 5.183594 19.277344 7.273437 29.9375zm0 0"></path><path d="m174.71875 313.027344c-11.398438 3.957031-22.363281 7.277344-32.726562 9.9375 12.152343 41.007812 30.265624 67.035156 50.503906 67.035156 3.140625-.011719 6.25-.621094 9.160156-1.800781-11.304688-17.089844-20.679688-43.277344-26.9375-75.171875zm0 0"></path></g><path d="m192.496094 400c-15.761719 0-38.320313-10.816406-56.292969-62.355469-12.871094-36.917969-19.960937-85.800781-19.960937-137.644531s7.089843-100.726562 19.960937-137.644531c17.972656-51.539063 40.53125-62.355469 56.292969-62.355469 15.757812 0 38.316406 10.816406 56.289062 62.355469 12.871094 36.917969 19.960938 85.800781 19.960938 137.644531s-7.089844 100.726562-19.960938 137.644531c-17.972656 51.539063-40.53125 62.355469-56.289062 62.355469zm0-380c-12.738282 0-26.71875 18.300781-37.402344 48.941406-12.15625 34.859375-18.851562 81.398438-18.851562 131.058594s6.695312 96.199219 18.847656 131.058594c10.6875 30.640625 24.667968 48.941406 37.40625 48.941406 12.734375 0 26.71875-18.300781 37.398437-48.941406 12.15625-34.855469 18.847657-81.402344 18.847657-131.058594s-6.691407-96.199219-18.847657-131.058594c-10.679687-30.640625-24.664062-48.941406-37.398437-48.941406zm0 0"></path><path d="m333.808594 209.917969c-3.703125-3.308594-7.558594-6.613281-11.566406-9.917969 3.996093-3.300781 7.851562-6.609375 11.566406-9.917969 19.304687-17.246093 43.539062-42.726562 49.371094-66.171875 3.25-13.070312-.15625-22.019531-3.582032-27.222656-11.710937-17.765625-41.769531-22-86.9375-12.222656-30.136718 6.519531-64.722656 18.761718-100.164062 35.300781-35.445313-16.535156-70.027344-28.777344-100.167969-35.300781-45.164063-9.773438-75.226563-5.546875-86.933594 12.222656-3.429687 5.199219-6.835937 14.152344-3.582031 27.222656 5.828125 23.445313 30.066406 48.925782 49.371094 66.171875 3.703125 3.308594 7.554687 6.613281 11.5625 9.917969-3.996094 3.300781-7.851563 6.609375-11.5625 9.917969-19.304688 17.246093-43.542969 42.726562-49.371094 66.171875-3.253906 13.070312.152344 22.019531 3.582031 27.222656 7.597657 11.53125 22.925781 17.359375 45.097657 17.359375 11.992187 0 25.988281-1.707031 41.84375-5.136719 30.136718-6.519531 64.71875-18.761718 100.164062-35.300781 35.441406 16.535156 70.027344 28.777344 100.164062 35.300781 15.855469 3.429688 29.847657 5.136719 41.839844 5.136719 22.167969 0 37.5-5.828125 45.101563-17.359375 3.425781-5.199219 6.832031-14.152344 3.582031-27.222656-5.839844-23.445313-30.074219-48.925782-49.378906-66.171875zm1.3125-110.625c14.542968 0 24.199218 2.96875 27.773437 8.398437.664063 1.011719 2.691407 4.085938.875 11.390625-2.078125 8.363281-10.464843 26.765625-43.289062 56.085938-4.53125 4.050781-9.316407 8.101562-14.3125 12.144531-23.742188-17.726562-48.730469-33.714844-74.773438-47.84375-5.210937-2.851562-10.425781-5.617188-15.648437-8.300781 51.085937-22.234375 93.175781-31.875 119.375-31.875zm-45.402344 100.707031c-21.660156 15.820312-44.351562 30.183594-67.917969 42.988281-9.992187 5.464844-19.757812 10.507813-29.300781 15.128907-9.53125-4.628907-19.304688-9.671876-29.3125-15.128907-23.570312-12.804687-46.257812-27.167969-67.914062-42.988281 21.660156-15.820312 44.347656-30.183594 67.914062-42.988281 10-5.46875 19.769531-10.511719 29.308594-15.125 9.53125 4.628906 19.300781 9.671875 29.304687 15.125 23.566407 12.804687 46.257813 27.167969 67.917969 42.988281zm-225.210938-24.832031c-32.820312-29.320313-41.207031-47.726563-43.289062-56.085938-1.816406-7.304687.210938-10.378906.875-11.390625 3.582031-5.429687 13.226562-8.398437 27.773438-8.398437 26.199218 0 68.292968 9.640625 119.371093 31.875-5.21875 2.683593-10.433593 5.449219-15.648437 8.300781-26.039063 14.128906-51.027344 30.117188-74.769532 47.839844-4.992187-4.039063-9.78125-8.089844-14.3125-12.140625zm-42.414062 117.140625c-.664062-1.011719-2.691406-4.085938-.875-11.390625 2.082031-8.359375 10.46875-26.765625 43.289062-56.085938 4.53125-4.050781 9.320313-8.101562 14.3125-12.140625 23.742188 17.722656 48.730469 33.710938 74.769532 47.839844 5.210937 2.851562 10.429687 5.621094 15.652344 8.300781-79.441407 34.582031-137.125 38.6875-147.148438 23.476563zm340.800781 0c-3.578125 5.429687-13.222656 8.398437-27.769531 8.398437-26.203125 0-68.292969-9.640625-119.371094-31.875 5.21875-2.683593 10.433594-5.449219 15.648438-8.300781 26.039062-14.128906 51.027344-30.117188 74.765625-47.839844 5 4.042969 9.773437 8.089844 14.316406 12.140625 32.820313 29.320313 41.207031 47.726563 43.285156 56.085938 1.816407 7.304687-.210937 10.382812-.875 11.390625zm0 0"></path></svg>
<h3 style="color: black; font-size: 18px;" class="title">3D Model</h3>
<div class="list">
<ul>
<li style="font-size: 16px; font-family: Berlin Sans FB Demi Bold"><span>3DsMax</span></li>
<li><span>Maya</span></li>
</ul>
</div>
</div>
</li>
<li>
<div class="list_inner">
<svg xmlns="http://www.w3.org/2000/svg" height="419pt" viewBox="0 -9 419.55413 419" width="419pt" class="svg replaced-svg"><path d="m156.023438 353.625 13.863281-80.835938-99.886719-97.367187 138.042969-20.058594 36.671875-74.300781-34.9375-70.785156-61.734375 125.085937-138.042969 20.058594 99.886719 97.367187-23.578125 137.488282zm0 0" fill="none"></path><path d="m333.246094 400.277344c-1.621094 0-3.21875-.394532-4.652344-1.148438l-118.816406-62.46875-118.816406 62.46875c-3.367188 1.769532-7.453126 1.476563-10.53125-.761718-3.082032-2.238282-4.621094-6.03125-3.980469-9.78125l22.695312-132.300782-96.125-93.699218c-2.726562-2.65625-3.707031-6.628907-2.53125-10.25 1.175781-3.621094 4.304688-6.257813 8.074219-6.804688l132.839844-19.300781 59.40625-120.378907c1.6875-3.414062 5.164062-5.574218 8.96875-5.574218 3.804687 0 7.28125 2.160156 8.964844 5.574218l59.410156 120.375 132.839844 19.296876c3.765624.550781 6.894531 3.1875 8.074218 6.808593 1.175782 3.617188.195313 7.589844-2.53125 10.25l-96.125 93.699219 22.691406 132.300781c.5 2.902344-.308593 5.882813-2.210937 8.136719-1.898437 2.253906-4.695313 3.554688-7.644531 3.554688zm-123.46875-84.917969c1.621094 0 3.21875.394531 4.652344 1.148437l105.535156 55.484376-20.15625-117.511719c-.554688-3.242188.519531-6.550781 2.878906-8.851563l85.378906-83.222656-117.988281-17.144531c-3.257813-.472657-6.074219-2.519531-7.53125-5.472657l-52.769531-106.917968-52.769532 106.917968c-1.457031 2.953126-4.269531 5-7.527343 5.472657l-117.992188 17.144531 85.378907 83.222656c2.359374 2.300782 3.433593 5.609375 2.875 8.851563l-20.152344 117.515625 105.535156-55.480469c1.433594-.757813 3.03125-1.152344 4.652344-1.15625zm0 0"></path></svg>
<h3 style="color: black; font-size: 18px;" class="title">Graphic Design</h3>
<div class="list">
<ul>
<li><span>Illustrator</span></li>
<li><span>Photoshop</span></li>
<li><span>After Effect</span></li>
<li><span>Premiere</span></li>
</ul>
</div>
</div>
</li>
</ul>
</div>
</div>
</div>
</div>


<div class="arlo_tm_portfolio_titles"></div>
<div id="portfolio" class="arlo_tm_section">
<div class="section_inner">
<div class="arlo_tm_portfolio">
<div class="portfolio_list">
<div class="arlo_tm_title">
<h3 style="background-color: #2e5abc; color: white; font-size: 16px;">Creative Portfolio</h3>
</div>
<div class="portfolio_filter">
<ul>
<li style="font-size: 18px;"><a href="#" class="current" data-filter="*">All</a></li>
@foreach($project_categories as $project_category)
<li style="font-size: 18px; font-family: Arial"><a href="#" data-filter=".{{$project_category->slug}}">{{$project_category->name}}</a></li>
@endforeach

</ul>
</div>
<ul class="portfolio_item gallery_zoom">
    @foreach($projects as $project)
    

@php
    $category = $project_categories->where('slug', $project->type)->first();
@endphp
<li class="modalbox {{ $project->type }}">
<div class="inner">
<div class="entry arlo_tm_portfolio_animation_wrap" data-title="{{ $project->name }}" data-category="{{ $category->name }}">
<a class="popup_info portfolio_popup" href="#">
<img src="{{ $project->image }}" style="height: 240px;object-fit: cover;" alt />
<div class="abs_image" data-img-url="{{ $project->image }}"></div>
</a>
</div>

<div class="hidden_content_portfolio">
<div class="popup_details">
<div class="main_details">
<div class="textbox">
{!! $project->content !!}
</div>
<div class="detailbox">
<ul>
<li>
<span class="first">Client</span>
<span>{{ $profile->name }}</span>
</li>
<li>
<span class="first">Category</span>
<span><a href="#">{{ $category->name }}</a></span>
</li>
<li>
<span class="first">Date</span>
<span>{{ $project->created_at->format('M d, Y h:i A') }}</span>
</li>
<li>
<span class="first">Share</span>
<ul class="share">
<li><a href="#"><img class="svg" src="{{asset('img/svg/social/facebook.svg')}}" alt /></a></li>
</li>
</ul>
</li>
</ul>
</div>
</div>
</div>
</div>
</div>
</li>


    @endforeach 

</ul>
</div>
</div>
</div>
</div>


<div id="blog" class="arlo_tm_section">
<div class="section_inner">
<div class="arlo_tm_news">
<div class="news_list">
<div class="arlo_tm_title">
<h3 style="background-color: #2e5abc; color: white; font-size: 16px;">Recent Posts</h3>
</div>
<ul>
@foreach($blogs as $blog)
<li>
<div class="list_inner">
<div class="image">
<img src="{{$blog->image}}" alt />
<div class="main" data-img-url="{{$blog->image}}"></div>
<a class="arlo_tm_full_link" href="#"></a>
<div style="font-family: Berlin Sans FB Demi Bold;" class="date"><span>{{ $blog->created_at->format('M d, Y h:i A') }}</span></div>
</div>
<div class="desc">
<div class="meta">
<span>By <a class="line_effect" href="#">{{$blog->author}}</a></span><span>In <a class="line_effect" href="#">{{$blog->category}}</a></span>
</div>
<div class="title">
<h3 style="font-size: 18px; font-family: Berlin Sans FB Demi Bold;"><a class="text_hover_effect" href="#">{{$blog->title}}</a></h3>
</div>
<div class="arlo_tm_button">
<a href="#">
<span class="back">Read More</span>
<span style="background-color: #2e5abc; font-size: 18px;" class="front"class="front">Read More</span>
</a>
</div>
</div>

<div class="news_hidden_details">
<div class="news_popup_informations">
<div class="text">
{!! $blog->content !!}
</div>
</div>
</div>

</div>
</li>
@endforeach
</ul>
</div>
</div>
</div>
</div>


<div id="contact" class="arlo_tm_section">
<div class="section_inner">
<div class="arlo_tm_contact">
<div class="contact_inner">
<div class="arlo_tm_title">
<h3 style="background-color: #2e5abc; color: white; font-size: 16px;">Get in Touch</h3>
</div>
<div class="my_map">
<div class="mapouter"><div class="gmap_canvas">
{!! $profile->gg_map !!}
<a href="https://123movies-i.net"></a><br><style>.mapouter{position:relative;text-align:right;height:350px;width:100%;}</style><a href="https://www.embedgooglemap.net">embed google map</a><style>.gmap_canvas {overflow:hidden;background:none!important;height:350px;width:100%;}</style></div></div>

</div>
<div class="form_wrapper">
<form id="contactForm">
<div class="error_box" id="empty-form">
<p>Please Fill Required Fields</p>
</div>
<div class="error_box" id="subject-alert">
<p>Please Select Subject</p>
</div>
<div class="error_box" id="security-alert">
<p>Security code does not match !</p>
</div>
<div class="error_box" id="email-invalid">
<p>Please enter a valid email address. Exp. <a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="62071a030f120e0722050f030b0e4c010d0f">[email&#160;protected]</a></p>
</div>
<div class="error_box" id="phone-invalid">
<p>Please enter a valid phone number.Exp. +998994425557</p>
</div>
<div class="error_box" id="error_mail">
<p></p>
</div>
<div class="success_box" id="success_mail">
<p>Your message has been sent. We will contact you as soon as possible.</p>
</div>
<ul>
<li>
<input type="text" placeholder="Name" name="contact_name" class="cf-form-control" />
<span></span>
</li>
<li>
<input type="text" placeholder="Email" name="contact_email" class="cf-form-control" />
<span></span>
</li>
<li>
<input type="text" placeholder="Phone" name="contact_phone" class="cf-form-control" />
<span></span>
</li>
<li>
<select name="contact_subject" class="cf-form-control colored">
<option value="Designer">Designer</option>
<option value="Web Development">Web Development</option>
<option value="Game Developer">Game Developer</option>
<option value="UI/UX Design">UI/UX Design</option>
</select>
</li>
<li id="text-area-w">
<textarea placeholder="Message" name="contact_message" class="cf-form-control"></textarea>
</li>
<li id="enter_code">
<span id="txtCaptchaSpan"></span>
<input type="text" class="cf-form-control" name="contact_question" id="txtInput" autocomplete="off" placeholder="Please Enter Code *">
<input type="hidden" id="txtCaptcha">
</li>
</ul>
<div class="arlo_tm_button">
<a href="#" id="send_message">
<span class="back">Send Message</span>
<span style="background-color: black; font-size: 18px;" class="front">Send Message</span>
</a>
</div>
</form>
</div>
</div>
</div>
</div>
</div>

</div>
</div>

</div>


<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script src="js/jquery.js"></script>
<!--[if lt IE 10]> <script type="text/javascript" src="js/ie8.js"></script> <![endif]-->
<script src="js/plugins.js"></script>
<script src="js/contact.form.js"></script>
<script src="js/init.js"></script>

</body>
</html>
