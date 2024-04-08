# Remove files from destination that are not present in source
find ~/../../xampp/htdocs/src -mindepth 1 -maxdepth 2 -type f -exec sh -c '
    src_file="src/$(basename "$1")"
    if [ ! -e "$src_file" ]; then
        echo "Removing $1"
        rm "$1"
    fi
' sh {} \;

# Copy updated files from source to destination
cp -ur src/* ~/../../xampp/htdocs/src