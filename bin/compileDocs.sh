#!/bin/bash

OUTPUT_FOLDER=out/doc
OUTPUT_SPECS_FOLDER="$OUTPUT_FOLDER"/specs
SPEC_FILE=$1
DOCS_FOLDER=$2

if [[ ! -d $OUTPUT_FOLDER ]]; then
	mkdir -p "$OUTPUT_FOLDER"
else
	rm -rf ${OUTPUT_FOLDER:?}/*
fi

cp -r "${DOCS_FOLDER:?}"/* "${OUTPUT_FOLDER:?}"
npx -y --verbose @redocly/cli bundle \
	"$SPEC_FILE" \
	--output "$OUTPUT_SPECS_FOLDER/$(basename "$SPEC_FILE")"