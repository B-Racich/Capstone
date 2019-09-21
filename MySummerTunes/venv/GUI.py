from __future__ import unicode_literals
from tkinter import *
from FileSystem import FileSystem


class GUI:

    def __init__(self, master):
        self.master = master
        self.file_system = FileSystem(self)
        master.title("My Summer Tunes")
        self.status_label_text = StringVar()
        self.video_title_text = StringVar()
        self.location_label_text = StringVar()

        #Left panel
        left_panel = Frame(master)
        left_panel.grid(row=0, column=0)

        """URL"""
        self.urlLabel = Label(left_panel, text="Youtube URL: ")
        self.urlLabel.grid(row=0, column=0)

        self.urlEntry = Entry(left_panel)
        self.urlEntry.grid(row=0, column=1)

        """TITLE"""
        self.videoLabel = Label(left_panel, text="Video Title:  ")
        self.videoLabel.grid(row=1, column=0)

        self.videoTitle = Label(left_panel, textvariable=self.video_title_text)
        self.videoTitle.grid(row=1, column=1)

        """TYPE"""
        self.videoTypeLabel = Label(left_panel, text="Video Type:  ")
        self.videoTypeLabel.grid(row=2, column=0)

        self.videoType = Label(left_panel)
        self.videoType.grid(row=2, column=1)

        """DOWNLOAD BTN"""
        self.downloadBtn = Button(left_panel, text="Download", command=self.file_system.youtube_download)
        self.downloadBtn.grid(row=3, column=0)

        self.statusLabel = Label(left_panel, textvariable=self.status_label_text)
        self.statusLabel.grid(row=3, column=1)

        """DOWNLOAD LOCATION BTN"""
        self.setDownloadsBtn = Button(left_panel, text="Set Download Location", command=self.file_system.set_download_path)
        self.setDownloadsBtn.grid(row=4, column=0)

        self.locationLabel = Label(left_panel, textvariable=self.location_label_text)
        self.locationLabel.grid(row=4, column=1)

        self.set_ver_num = Label(left_panel, text='0.1.0')
        self.set_ver_num.grid(row=5, column=0)

        #Right panel
        right_panel = Frame(master)
        right_panel.grid(row=0, column=1)

        self.logList = Listbox(right_panel)
        self.logList.grid(row=0, column=0)

        """INIT"""
        self.location_label_text.set(self.file_system.get_download_path())

    def getUrl(self):
        return self.urlEntry.get()


root = Tk()
app = GUI(root)
root.mainloop()
