<?php
function e(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function excerpt(string $html, int $len = 180): string {
  // Strip tags so excerpts don’t break the layout
  $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
  if (mb_strlen($text) <= $len) return $text;
  return mb_substr($text, 0, $len) . '…';
}

function redirect(string $url): never {
  header("Location: $url");
  exit;
}
