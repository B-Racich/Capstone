import youtube_dl
from pytube import YouTube


class Media:

    def __init__(self, url):
        # if str(url).find('&'):
        #     url = str(url).split('&')[0]
        self.url = url
        yt = YouTube(url)
        self.title = yt.title

    def download(self, path):
        ydl_opts = {
            'format': 'bestaudio/best',
            'postprocessors': [{
                'key': 'FFmpegExtractAudio',
                'preferredcodec': 'vorbis',
                'preferredquality': '192',
            }],
            'outtmpl': path
        }
        with youtube_dl.YoutubeDL(ydl_opts) as ydl:
            ydl.download([self.url])

    def toString(self):
        print(self.url, self.title)
