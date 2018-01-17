<!DOCTYPE html>
<html>
<head>
  <title>AldanaIO: IIT Online Scraper</title>
  <style type="text/css">
    :root {
      --main-bg: black;
      --main-fg: white;
      --main-bg-tint: #2b2b2b;
    }
    body {
      background-color: var(--main-bg);
      color: var(--main-fg);
      font-family: "Trebuchet MS", Helvetica, sans-serif;
    }
    a {
      color: lime;
      text-decoration: none;
    }
    a:hover {
      text-decoration: underline;
    }
    header,
    body > section,
    footer {
      max-width: 800px;
      margin: auto;
      margin-top: 40px;
    }
    header {
      text-align: center;
    }
    video {
      display: block;
    }
    aside.modal {
      position: fixed;
      background-color: rgba(0, 0, 0, 0.75);
      left: 0;
      right: 0;
      top: 0;
      bottom: 0;
      padding: 100px;
      text-align: center;
    }
    aside.modal h3 {
      position: absolute;
      top: 25px;
      left: 25px;
    }
    #modal-content {
      border-radius: 20px;
      background-color: var(--main-bg-tint);
      display: inline-block;
      /*
      width: calc(100vw - 200px);
      height: calc(100vh - 200px);
      */
      text-align: center;
    }
    video {
      width: auto;
      margin: auto;
      max-height: calc(100vh - 200px);
    }
    .hidden {
      display: none;
    }
  </style>
</head>
<body>
  <header>
    <h1>AldanaIO: IIT Online Scraper</h1>
  </header>

<?php
function formatBytes($size) {
  if ($size === 0) {
    return "0 Bytes";
  }
  $sizes = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
  return (round($size/pow(1024, ($i = floor(log($size, 1024)))), $i > 1 ? 2 : 0) . $sizes[$i]);
}

$root = "media/";
foreach (scandir($root) as $class) {
  $target = "{$root}{$class}";
  if ($class == "." || $class == ".." || !is_dir($target)) {
    continue;
  }
  echo "
  <section>
    <h2>{$class}</h2>
    <ul>
  ";

  foreach (scandir($target) as $file) {
    if ($file == "." || $file == "..") {
      continue;
    }
    $media = "{$target}/{$file}";
    $time = filemtime($media);
    $fs = formatBytes(filesize($media));
    echo "
      <li><a href='{$media}' data-time='{$time}' data-fs='{$fs}'>{$file}</a></li>
    ";
  }

  echo "
    </ul>
  </section>
  ";
}
?>

<aside class="modal hidden">
  <section id="modal-content">
    <h3></h3>
    <video controls></video>
  </section>
</aside>

<footer>
  <p>
    <a href='https://github.com/jpaldana/iit-online-scraper/'>Source Code @ GitHub</a> <span id='last-run' data-time='<?php echo filemtime("log.txt"); ?>'></span>
  </p>
  <a class="pushbullet-subscribe-widget" data-channel="aldanaio-iit" data-widget="button" data-size="small"></a>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
<script>
$(function() {
  $("li a").each(function() {
    var ts = moment.unix($(this).attr("data-time")).fromNow();
    var fs = $(this).attr("data-fs");
    $(this).text($(this).text() + " - " + ts + " - " + fs);
  });
  $("li a").on("click", function(e) {
    e.preventDefault();
    $("aside.modal").removeClass("hidden");
    $("aside.modal h3").text($(this).text());
    $("aside.modal video").append("<source src='" + $(this).attr("href") + "' type='video/mp4'>");
  });
  $("video").on("click", function(e) {
    if ($(this)[0].paused) {
      $(this)[0].play();
    }
    else {
      $(this)[0].pause();
    }
    e.preventDefault();
    e.stopPropagation();
  });
  $("modal-content").on("click", function(e) {
    e.preventDefault();
    e.stopPropagation();
  });
  $("aside.modal").on("click", function() {
    $(this).addClass("hidden");
  });
  var lastRun = moment.unix($("#last-run").attr("data-time")).fromNow();
  $("#last-run").text(" - last run: " + lastRun);
});

(function(){var a=document.createElement('script');a.type='text/javascript';a.async=true;a.src='https://widget.pushbullet.com/embed.js';var b=document.getElementsByTagName('script')[0];b.parentNode.insertBefore(a,b);})();
</script>
</body>
</html>