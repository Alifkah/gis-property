import urllib.request
import re

url = "https://cdnjs.cloudflare.com/ajax/libs/tabler-icons/3.44.0/tabler-icons-filled.min.css"
try:
    with urllib.request.urlopen(url) as response:
        content = response.read().decode('utf-8')
    print("Full length of filled CSS:", len(content))
    # find all matches of class starting with ti-heart
    matches = re.findall(r"\.ti-heart[a-zA-Z0-9\-]*", content)
    print("Matches starting with ti-heart in filled CSS:", sorted(list(set(matches))))
except Exception as e:
    print("Error:", e)
