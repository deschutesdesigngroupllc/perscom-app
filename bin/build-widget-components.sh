#!/bin/bash

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
COMPONENTS_DIR="$PROJECT_ROOT/resources/js/components"
DIST_DIR="$PROJECT_ROOT/resources/js/dist/components"

if [ ! -d "$COMPONENTS_DIR" ]; then
    echo "Error: Components directory not found at $COMPONENTS_DIR"
    exit 1
fi

if [ ! -d "$DIST_DIR" ]; then
    echo "Creating dist directory at $DIST_DIR"
    mkdir -p "$DIST_DIR"
fi

if ! command -v ncc &> /dev/null; then
    echo "Error: ncc is not installed. Please install it with: npm install -g @vercel/ncc"
    exit 1
fi

component_count=0
built_count=0

for dir in "$COMPONENTS_DIR"/*/; do
    if [ ! -d "$dir" ]; then
        continue
    fi
    
    component_count=$((component_count + 1))
    dirname=$(basename "$dir")
    echo "Building component: $dirname"
    
    if [ ! -f "$dir/index.js" ]; then
        echo "  Warning: index.js not found in $dirname, skipping..."
        continue
    fi
    
    output_dir="$DIST_DIR/$dirname"
    
    if ! (cd "$dir" && ncc build index.js -o "$output_dir" -m); then
        echo "  Error: Failed to build component $dirname"
        continue
    fi
    
    built_count=$((built_count + 1))
    echo "  ✓ Built successfully to $output_dir"
done

echo ""
echo "Build complete: $built_count/$component_count components built successfully"

if [ $built_count -eq 0 ] && [ $component_count -gt 0 ]; then
    echo "Warning: No components were built successfully"
    exit 1
fi