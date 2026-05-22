#!/usr/bin/env python3
"""Batch remove backgrounds from images using rembg (runs locally, no uploads)."""

import argparse
from pathlib import Path
from rembg import remove
from PIL import Image
import sys

SUPPORTED_EXTENSIONS = {".jpg", ".jpeg", ".png", ".webp", ".bmp", ".tiff", ".tif"}


def process_image(input_path: Path, output_path: Path, quality: int) -> None:
    with Image.open(input_path) as img:
        result = remove(img)
        result.save(output_path, "PNG" if output_path.suffix.lower() == ".png" else output_path.suffix[1:].upper(),
                     quality=quality)
    print(f"  ✓ {input_path.name} -> {output_path}")


def main():
    parser = argparse.ArgumentParser(description="Batch remove image backgrounds locally.")
    parser.add_argument("input", type=str, help="Input image file or directory")
    parser.add_argument("-o", "--output", type=str, default=None,
                        help="Output file or directory (default: input_parent/input_no_bg/)")
    parser.add_argument("-q", "--quality", type=int, default=100,
                        help="Output quality 1-100 (default: 100, no quality loss for PNG)")
    parser.add_argument("--overwrite", action="store_true", help="Overwrite existing output files")
    args = parser.parse_args()

    input_path = Path(args.input)
    if not input_path.exists():
        print(f"Error: {input_path} does not exist.", file=sys.stderr)
        sys.exit(1)

    if args.output:
        output_path = Path(args.output)
    else:
        output_path = input_path.parent / f"{input_path.stem}_no_bg" if input_path.is_file() else input_path / "no_bg"

    if input_path.is_file():
        files = [input_path] if input_path.suffix.lower() in SUPPORTED_EXTENSIONS else []
        if args.output:
            output_root = output_path.parent if output_path.suffix else output_path
        else:
            output_root = input_path.parent
    else:
        files = [f for f in input_path.rglob("*") if f.suffix.lower() in SUPPORTED_EXTENSIONS]
        output_root = output_path

    if not files:
        print("No supported image files found.", file=sys.stderr)
        sys.exit(1)

    output_root.mkdir(parents=True, exist_ok=True)
    print(f"Processing {len(files)} image(s) into {output_root} ...")

    for idx, file in enumerate(files, 1):
        rel = file.relative_to(input_path) if input_path.is_dir() else file.name
        out_file = output_root / rel.parent if rel != file.name else output_root
        if input_path.is_dir():
            out_file = output_root / rel
        else:
            out_file = output_root / f"{file.stem}_no_bg.png" if not args.output else output_root

        if out_file.exists() and not args.overwrite:
            print(f"  - Skipping {file.name} (output exists, use --overwrite)")
            continue

        out_file.parent.mkdir(parents=True, exist_ok=True)
        out_file = out_file.with_suffix(".png")
        process_image(file, out_file, args.quality)

    print("Done.")


if __name__ == "__main__":
    main()
