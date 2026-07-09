$headers = @{"User-Agent" = "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"}

Invoke-WebRequest -Uri "https://upload.wikimedia.org/wikipedia/en/1/1e/Baseball_%28crop%29.jpg" -Headers $headers -OutFile "c:\laragon\www\facility_booking\uploads\wiki_baseball.jpg"
Invoke-WebRequest -Uri "https://upload.wikimedia.org/wikipedia/commons/1/1c/Badminton_racket.jpg" -Headers $headers -OutFile "c:\laragon\www\facility_booking\uploads\wiki_badminton.jpg"
Invoke-WebRequest -Uri "https://upload.wikimedia.org/wikipedia/commons/d/d4/Table_tennis_racket_035.jpg" -Headers $headers -OutFile "c:\laragon\www\facility_booking\uploads\wiki_pingpong_paddle.jpg"
Invoke-WebRequest -Uri "https://upload.wikimedia.org/wikipedia/commons/3/36/Table_tennis_ball.jpg" -Headers $headers -OutFile "c:\laragon\www\facility_booking\uploads\wiki_pingpong_ball.jpg"

Write-Output "Downloaded remaining images"
