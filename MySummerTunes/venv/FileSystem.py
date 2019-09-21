from tkinter.filedialog import askdirectory
import os
from Media import Media
import html


class FileSystem:

    def __init__(self, gui):
        self.gui = gui
        self.appdata_path = os.getenv('LOCALAPPDATA')
        self.data_path = self.appdata_path + '\\MySummerTunes'

        if os.path.exists(self.data_path):
            self.log_path = self.data_path + '\\Tunes Log.txt'
            self.settings_path = self.data_path + '\\Settings.txt'
        else:
            os.mkdir(self.data_path)
            self.log_path = self.data_path + '\\Tunes Log.txt'
            self.settings_path = self.data_path + '\\Settings.txt'

    """
    Sets the download path
    """
    def set_download_path(self):
        download_path = askdirectory()
        has_replaced = False
        with open(self.settings_path, "r") as f:
            lines = f.readlines()
        with open(self.settings_path, "w") as f:
            for line in lines:
                if "downloads = " not in line:
                    f.write(line)
                elif "downloads = " in line:
                    f.write("downloads = "+download_path+'\n')
                    has_replaced = True
        if not has_replaced:
            file = open(self.settings_path, 'a')
            file.write('downloads = ' + download_path)
            file.close()

    """
    Returns the set download path or false if not set
    """
    def get_download_path(self):
        settings_file = open(self.settings_path, 'r')
        for line in settings_file:
            if 'downloads = ' in line:
                settings_file.close()
                return line.split('=')[1].strip()
        settings_file.close()
        return False

    """
    Checks if the log contains the track, returns boolean
    """
    def log_contains(self, item):
        log_file = open(self.log_path, 'r', encoding='utf-8')
        for line in log_file:
            if item in line:
                log_file.close()
                return True
        log_file.close()
        return False

    """
    Adds the track to log
    """
    def log_add(self, track):
        track_number = self.find_next_track_num()
        with open(self.log_path, "a", encoding='utf-8') as f:
            line = str(track) + '\t:ENCODED TRACK NAME:\t' + 'track'+str(track_number) + '\n'
            f.write(line)
        self.rename_track(track, track_number)

    """
    Removes the track from log
    """
    def log_remove(self, item):
        with open(self.log_path, "r") as f:
            lines = f.readlines()
        with open(self.log_path, "w") as f:
            for line in lines:
                if item not in line:
                    f.write(line)

    """Finds the next track number"""
    def find_next_track_num(self):
        current = 1
        with open(self.log_path, "r", encoding='utf-8') as f:
            lines = f.readlines()
            for line in lines:
                delim = "\t:ENCODED TRACK NAME:\t"
                if delim in line:
                    track_num = line.split("\t:ENCODED TRACK NAME:\t")[1]
                    track_num = track_num.strip('track')
                    if int(track_num) == current:
                        current = current+1
        return current

    """Rename track"""
    def rename_track(self, track, track_num):
        if "/" in track:
            track = track.replace("/", "_")
        elif "\"" in track:
            track = track.replace("\"", "'")
        src = self.get_download_path()+'/'+track+'.ogg'
        dst = self.get_download_path()+'/track'+str(track_num)+'.ogg'
        os.rename(src, dst)

    """
    Downloads the youtube video audio via URL
    """
    def youtube_download(self):
        # Get video
        url = self.gui.getUrl()
        media = Media(url)
        title = html.unescape(media.title)
        self.gui.video_title_text.set(title)

        # Check if downloaded
        if not self.log_contains(title):
            self.gui.status_label_text.set("Downloading song...")
            path = self.get_download_path() + '/%(title)s.%(ext)s'
            media.download(path)
            self.log_add(title)
            self.gui.status_label_text.set("Added to log")

        else:
            self.gui.status_label_text.set("Song already downloaded!")
