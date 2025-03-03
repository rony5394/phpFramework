#!/bin/bash

# This is NOT intended to be readable

DIRS=(
  "tests"
  "example/tests"
)

for DIR in "${DIRS[@]}"; do
  if [ -d "$DIR" ]; then
    for file in "$DIR"/*.php; do
      if [ -f "$file" ]; then
        php "$file"
        EXIT_CODE=$?
        
        if [ $EXIT_CODE -ne 0 ]; then
          exit $EXIT_CODE
        fi
      fi
    done
  fi
done

exit 0
