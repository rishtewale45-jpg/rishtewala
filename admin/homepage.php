<?php
require __DIR__.'/../config/config.php';
require_admin();

$keys = [
 'home_heading','home_subtitle','home_online_count','home_note',
 'home_profile_1_name','home_profile_1_city','home_profile_1_photo',
 'home_profile_2_name','home_profile_2_city','home_profile_2_photo',
 'home_profile_3_name','home_profile_3_city','home_profile_3_photo',
 'home_profile_4_name','home_profile_4_city','home_profile_4_photo',
 'home_profile_5_name','home_profile_5_city','home_profile_5_photo',
 'home_profile_6_name','home_profile_6_city','home_profile_6_photo',
 'female_banner_title','female_banner_subtitle','female_banner_image',
 'female_earning_title','female_earning_subtitle'
];

if($_SERVER['REQUEST_METHOD']==='POST'){
  foreach($keys as $k){
    if(isset($_POST[$k])){
      db()->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)')->execute([$k,trim($_POST[$k])]);
    }
  }
  if(!empty($_FILES['female_banner_upload']['name']) && $_FILES['female_banner_upload']['error']===0){
    $ext=strtolower(pathinfo($_FILES['female_banner_upload']['name'],PATHINFO_EXTENSION));
    if(in_array($ext,['png','jpg','jpeg','webp'],true)){
      $dir=__DIR__.'/../uploads/'; if(!is_dir($dir)) mkdir($dir,0755,true);
      $name='female_banner_'.time().'.'.$ext;
      if(move_uploaded_file($_FILES['female_banner_upload']['tmp_name'],$dir.$name)){
        db()->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE value=VALUES(value)')->execute(['female_banner_image','uploads/'.$name]);
      }
    }
  }
  $saved=true;
}

$defaults=[
 'home_heading'=>'Apna behtareen rishta dhoondhiye','home_subtitle'=>'Verified demo profiles • Safe & simple registration','home_online_count'=>'24+','home_note'=>'✨ Aaj naye profiles online hain — register karke matches dekhein',
 'female_banner_title'=>'स्वागत है','female_banner_subtitle'=>'आपका रजिस्ट्रेशन सफलतापूर्वक हो गया है।','female_banner_image'=>'https://randomuser.me/api/portraits/women/44.jpg',
 'female_earning_title'=>'Hamare Sath Judkar','female_earning_subtitle'=>'30 min Video Calling करके Extra Earning करें'
];
function hs($k){global $defaults; return h(setting($k,$defaults[$k]??''));}
?><!doctype html><html><head><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/style.css"></head><body><main class="phone">
<header><a href="index.php">←</a><b>Homepage Settings</b></header>
<section class="card"><p style="color:#666">Yahan se homepage ke banner, text, online count aur 6 demo profile cards change kar sakte ho.</p>
<?php if(isset($saved)):?><div class="success">Homepage settings updated.</div><?php endif;?>
<form method="post" enctype="multipart/form-data">
<h3>🏠 Homepage</h3>
<input name="home_heading" value="<?=hs('home_heading')?>" placeholder="Main heading">
<input name="home_subtitle" value="<?=hs('home_subtitle')?>" placeholder="Subtitle">
<input name="home_online_count" value="<?=hs('home_online_count')?>" placeholder="Online count e.g. 24+">
<input name="home_note" value="<?=hs('home_note')?>" placeholder="Bottom banner text">
<h3>👩 Demo Profiles</h3>
<?php for($i=1;$i<=6;$i++):?><div style="padding:10px 0;border-bottom:1px solid #eee"><b>Profile <?=$i?></b><input name="home_profile_<?=$i?>_name" value="<?=hs('home_profile_'.$i.'_name')?>" placeholder="Name"><input name="home_profile_<?=$i?>_city" value="<?=hs('home_profile_'.$i.'_city')?>" placeholder="City"><input name="home_profile_<?=$i?>_photo" value="<?=hs('home_profile_'.$i.'_photo')?>" placeholder="Photo URL (https://...) "></div><?php endfor;?>
<h3>👩 Female Home Banner</h3>
<input name="female_banner_title" value="<?=hs('female_banner_title')?>" placeholder="Banner title">
<input name="female_banner_subtitle" value="<?=hs('female_banner_subtitle')?>" placeholder="Banner subtitle">
<input name="female_banner_image" value="<?=hs('female_banner_image')?>" placeholder="Banner image URL">
<input type="file" name="female_banner_upload" accept="image/*">
<h3>📹 Earning Card</h3>
<input name="female_earning_title" value="<?=hs('female_earning_title')?>" placeholder="Earning title">
<input name="female_earning_subtitle" value="<?=hs('female_earning_subtitle')?>" placeholder="Earning subtitle">
<button>Save Homepage</button></form></section></main><script src="/rishtewale-protection.js"></script></body></html>
