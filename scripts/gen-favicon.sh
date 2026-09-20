#!/usr/bin/env bash

prefix="favicon"
source "$(dirname "${BASH_SOURCE[0]}")/resize-img.sh"

mv "${dst_dir}/${prefix}-180x180.${format}" "${dst_dir}/apple-touch-icon.png"
magick "${src_file_path}" -define icon:auto-resize=16,32,48 "${dst_dir}/favicon.ico"
