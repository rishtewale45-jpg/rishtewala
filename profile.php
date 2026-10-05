<?php
require __DIR__.'/config/config.php';

if(empty($_SESSION['user_id'])){
    header('Location:index.php');
    exit;
}

$id=(int)($_GET['id']??0);

$s=db()->prepare('SELECT * FROM profiles WHERE id=? AND active=1');
$s->execute([$id]);
$p=$s->fetch();

if(!$p) die('Profile not found');

$u=db()->prepare('SELECT * FROM users WHERE id=?');
$u->execute([$_SESSION['user_id']]);
$u=$u->fetch();

$price=$u['gender']==='male'
    ? setting('male_premium_price','199')
    : setting('female_premium_price','99');

/*
|--------------------------------------------------------------------------
| Main Image
|--------------------------------------------------------------------------
*/
$mainImage=$p['photo'];

if(!$mainImage){
    $mainImage=$p['gender']==='female'
        ? "https://randomuser.me/api/portraits/women/".(($p['id']*7)%90).".jpg"
        : "https://randomuser.me/api/portraits/men/".(($p['id']*7)%90).".jpg";
}

/*
|--------------------------------------------------------------------------
| Sub Images
|--------------------------------------------------------------------------
*/
$subImages=[];

try{
    $gi=db()->prepare(
        'SELECT image_path
         FROM profile_images
         WHERE profile_id=?
         ORDER BY sort_order ASC
         LIMIT 4'
    );

    $gi->execute([$id]);
    $subImages=$gi->fetchAll(PDO::FETCH_COLUMN);
}catch(Throwable $e){
    $subImages=[];
}

/*
|--------------------------------------------------------------------------
| Masked WhatsApp
|--------------------------------------------------------------------------
| Har profile ke liye alag stable number.
| Same profile refresh hone par number change nahi hoga.
|--------------------------------------------------------------------------
*/
$seed=abs(crc32('rishtewale-'.$id));

$maskedPrefixes=[
    '937387',
    '926272',
    '948383',
    '838627',
    '927361',
    '981274',
    '895463',
    '876352',
    '945821',
    '863729'
];

$maskedWhatsapp=$maskedPrefixes[$seed % count($maskedPrefixes)].'XXXX';
?>

<!doctype html>
<html>

<head>

<meta name="viewport" content="width=device-width,initial-scale=1">

<link rel="stylesheet" href="assets/style.css">

<title><?=h($p['name'])?></title>

<style>

.profile-gallery{
    margin-bottom:18px;
}

.profile-main-photo{
    width:100%;
    aspect-ratio:1/1;
    border-radius:18px;
    overflow:hidden;
    background:#eee;
}

.profile-main-photo img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
}

.profile-thumbs{
    display:grid;
    grid-template-columns:repeat(4,1fr);
    gap:8px;
    margin-top:10px;
}

.profile-thumb{
    aspect-ratio:1/1;
    border-radius:10px;
    overflow:hidden;
    border:2px solid transparent;
    background:#eee;
    padding:0;
}

.profile-thumb img{
    width:100%;
    height:100%;
    object-fit:cover;
    display:block;
}

.profile-thumb.active{
    border-color:#e91e63;
}

.whatsapp-card{
    display:flex;
    align-items:center;
    gap:10px;
    padding:13px;
    margin-top:15px;
    border-radius:14px;
    background:#f1fff5;
    border:1px solid #c9efd5;
}

.whatsapp-logo{
    width:40px;
    height:40px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:22px;
    background:#25D366;
}

.whatsapp-info{
    flex:1;
}

.whatsapp-info b{
    display:block;
}

.whatsapp-info small{
    color:#777;
}

.whatsapp-lock{
    font-size:13px;
    font-weight:600;
}

.locked{
    color:#777;
}

</style>

</head>

<body>

<main class="phone">

<header>
<a href="dashboard.php">←</a>
<b><?=h($p['name'])?></b>
</header>

<section class="detail">

<!-- PROFILE GALLERY -->

<div class="profile-gallery">

    <div class="profile-main-photo">
        <img
            id="mainProfileImage"
            src="<?=h($mainImage)?>"
            alt="<?=h($p['name'])?>"
        >
    </div>

    <?php if(!empty($subImages)): ?>

    <div class="profile-thumbs">

        <?php foreach($subImages as $index=>$img): ?>

        <button
            type="button"
            class="profile-thumb <?=($index===0?'active':'')?>"
            onclick="changeProfileImage(this,<?=json_encode($img)?>)"
        >
            <img src="<?=h($img)?>" alt="Profile photo">
        </button>

        <?php endforeach; ?>

    </div>

    <?php endif; ?>

</div>


<h1>
<?=h($p['name'])?>, <?=h($p['age'])?>
</h1>

<p>
📍 <?=h($p['city'])?>, <?=h($p['state'])?>
</p>


<!-- WHATSAPP -->

<div class="whatsapp-card">

    <div class="whatsapp-logo">
        💬
    </div>

    <div class="whatsapp-info">

        <b>WhatsApp</b>

        <span>
            <?=h($maskedWhatsapp)?>
        </span>

        <small>
            Premium members can view full number
        </small>

    </div>

    <div class="whatsapp-lock">
        🔒
    </div>

</div>


<div class="info">

<b>About Me</b>
<p><?=h($p['about'])?></p>

<b>Marital Status</b>
<p><?=h($p['marital_status'])?></p>

<b>Annual Income</b>
<p><?=h($p['annual_income'])?></p>

<b>Description</b>
<p><?=h($p['description'])?></p>

<b>Jaati / Caste</b>
<p class="locked">
•••••• 🔒 Premium
</p>

<b>Dharm / Religion</b>
<p class="locked">
•••••• 🔒 Premium
</p>

<b>WhatsApp</b>
<p class="locked">
<?=h($maskedWhatsapp)?> 🔒 Premium
</p>

</div>


<a
    class="button"
    href="premium.php?profile=<?=$id?>"
>
🔐 Premium Plan — ₹<?=h($price)?> →
</a>

</section>

</main>


<script>

function changeProfileImage(button,image){

    document.getElementById('mainProfileImage').src=image;

    document.querySelectorAll('.profile-thumb').forEach(function(el){
        el.classList.remove('active');
    });

    button.classList.add('active');
}

</script>

<script src="/rishtewale-protection.js"></script>

</body>
</html>
