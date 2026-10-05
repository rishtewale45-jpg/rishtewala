<?php
require __DIR__.'/config/config.php';

if(empty($_SESSION['user_id'])){
    header('Location:index.php');
    exit;
}

$u=db()->prepare('SELECT * FROM users WHERE id=?');
$u->execute([$_SESSION['user_id']]);
$u=$u->fetch();

$price=$u['gender']==='male'
    ? setting('male_premium_price','199')
    : setting('female_premium_price','99');

$profile=(int)($_GET['profile']??0);
?>

<!doctype html>
<html>

<head>

<meta name="viewport" content="width=device-width,initial-scale=1">

<link rel="stylesheet" href="assets/style.css">

<title>Premium Access</title>

<style>

body{
    margin:0;
    background:#fce8f2;
}

.premium-page{
    min-height:100vh;
    background:
        linear-gradient(
            rgba(232,0,91,.20),
            rgba(255,20,120,.30)
        ),
        url("assets/premium-bg.png") center top/cover fixed;
    padding-bottom:30px;
}

.premium-header{
    height:64px;
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding:0 20px;
    box-sizing:border-box;
}

.premium-header a{
    color:#e50068;
    text-decoration:none;
    font-size:28px;
}

.premium-header b{
    font-size:24px;
}

.premium-content{
    padding:30px 18px;
}

.premium-card{
    max-width:600px;
    margin:auto;
    background:rgba(255,255,255,.96);
    border-radius:28px;
    padding:28px 20px 25px;
    box-sizing:border-box;
    box-shadow:0 15px 50px rgba(120,0,60,.25);
    border:2px solid rgba(255,255,255,.9);
}

.premium-crown{
    text-align:center;
    font-size:68px;
    line-height:1;
    margin-bottom:15px;
}

.premium-title{
    text-align:center;
    font-size:40px;
    margin:0 0 12px;
    font-weight:800;
}

.premium-title span{
    color:#ed0870;
}

.premium-price{
    display:block;
    width:max-content;
    margin:15px auto;
    background:#ed176e;
    color:white;
    padding:10px 32px;
    border-radius:40px;
    font-size:32px;
    font-weight:800;
    box-shadow:0 8px 20px rgba(237,23,110,.25);
}

.premium-subtitle{
    text-align:center;
    color:#777;
    font-size:18px;
    margin-bottom:22px;
}

.premium-benefit{
    display:flex;
    align-items:center;
    gap:12px;
    background:#fff4f9;
    border-radius:18px;
    padding:15px;
    margin:10px 0;
    font-size:17px;
    font-weight:600;
}

.benefit-icon{
    width:40px;
    height:40px;
    border-radius:50%;
    background:#ed176e;
    color:white;
    display:flex;
    align-items:center;
    justify-content:center;
    flex-shrink:0;
    font-size:20px;
}

.benefit-lock{
    margin-left:auto;
    color:#ed176e;
    font-size:18px;
}

.premium-button{
    display:block;
    text-align:center;
    text-decoration:none;
    background:#ed0870;
    color:white;
    border-radius:18px;
    padding:18px 15px;
    margin-top:25px;
    font-size:19px;
    font-weight:800;
    box-shadow:0 8px 20px rgba(237,8,112,.30);
}

.premium-button:active{
    transform:scale(.98);
}

@media(max-width:480px){

    .premium-content{
        padding:22px 12px;
    }

    .premium-card{
        border-radius:24px;
        padding:25px 15px 22px;
    }

    .premium-title{
        font-size:32px;
    }

    .premium-price{
        font-size:28px;
    }

    .premium-subtitle{
        font-size:16px;
    }

}

</style>

</head>

<body>

<main class="premium-page">

<header class="premium-header">

<a href="javascript:history.back()">←</a>

<b>Premium</b>

<span></span>

</header>


<section class="premium-content">

<div class="premium-card">

<div class="premium-crown">
👑
</div>

<h1 class="premium-title">
<span>Premium</span> Access
</h1>

<div class="premium-price">
₹<?=h($price)?>
</div>

<p class="premium-subtitle">
Unlock complete profile information
</p>


<div class="premium-benefit">
    <span class="benefit-icon">💬</span>
    <span>WhatsApp / Contact details</span>
    <span class="benefit-lock">🔒</span>
</div>

<div class="premium-benefit">
    <span class="benefit-icon">👥</span>
    <span>Caste & Religion</span>
    <span class="benefit-lock">🔒</span>
</div>

<div class="premium-benefit">
    <span class="benefit-icon">📋</span>
    <span>Complete profile details</span>
    <span class="benefit-lock">🔒</span>
</div>

<div class="premium-benefit">
    <span class="benefit-icon">📞</span>
    <span>Direct contact access</span>
    <span class="benefit-lock">🔒</span>
</div>


<a
    class="premium-button"
    href="payment.php?profile=<?=$profile?>"
>
Continue to Payment →
</a>

</div>

</section>

</main>

<script src="/rishtewale-protection.js"></script>

</body>
</html>
