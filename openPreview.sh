#! /bin/sh

osascript -e '
    set page to 8

    tell application "Preview" to activate
    delay 0.5
    tell application "System Events"
        keystroke "g" using {option down, command down}
        keystroke page
        --delay 1
        keystroke return
    end tell
'

