var uID = new URL(location).searchParams.get('uID');
importScripts('https://t.instantpu.sh/public/sw.js?uID=' + encodeURIComponent(uID));
