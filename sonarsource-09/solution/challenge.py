def get_addon_path(self):
    # 1) `filename` and `tmp_token` is user input
    filename = os. path.basename(self.request.GET.get("my_file"))
    tmp_token = self.request.GET.get('my_token')
    # 2) when `tmp_token` = ../../other/path/on/system
    #    and  `filename` = myfilename
    #    then path = /other/path/on/system/myfilename
    path = os. path.join(tempfile.gettempdir(), tmp_token, filename)
    if not os. path.isfile(path):
        raise ValueError("Error! File not found.")
    if hasattr(os, "geteuid") and os.stat(path).st_uid != os.geteuid():
        raise ValueError("Error! File not owned by current user.")
    return path

@csrf_exempt
def form_valid(self, form):
    try:
        installer.install_package(self.get_addon_path())
        response["success"] = True
    except Exception:
        os.unlink(self.get_addon_path())
        response["success"] = False
