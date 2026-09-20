#!/usr/bin/env bash

if [ -z "$1" ]; then
  echo "Please specify the input file path as first argument"
  exit 1
fi

src_file_path="$1"
src_file_name="${src_file_path##*/}" # Remove everything before last /
src_base_name="${src_file_name%.*}" # Remove extention
dst_dir="$(dirname "${src_file_path}")"

if [ -z "$prefix" ]; then
  prefix="${src_base_name}"
fi

if [ -n "$2" ]; then
  prefix="$2"
fi

format="png"
sizes="16 32 48 180 192"

for size in ${sizes}; do
  magick "${src_file_path}" -resize ${size}x${size} "${dst_dir}/${prefix}-${size}x${size}.${format}"
done
