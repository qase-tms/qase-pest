#!/usr/bin/env python3
"""
Clean expected YAML files by removing dynamic fields that change between runs.

Removes:
- execution blocks (keeps only top-level status)
- Step IDs and timestamps (start_time, end_time, duration)
- Attachment IDs and file_path (keeps file_name and mime_type)
- Empty collections: fields: {}, attachments: [], params: {}, param_groups: [], steps: []
- muted: false (default value)
- message (contains dynamic timestamps and unstable whitespace)
- stacktrace (contains absolute file paths)
"""

import argparse
import sys
from pathlib import Path

import yaml


def clean_execution(execution: dict) -> dict | None:
    """Keep only top-level status from execution block."""
    if not execution:
        return None
    cleaned = {}
    if "status" in execution:
        cleaned["status"] = execution["status"]
    return cleaned if cleaned else None


def clean_steps(steps: list) -> list:
    """Clean step entries: remove IDs, timestamps, clean nested execution."""
    cleaned = []
    for step in steps:
        clean_step = {}

        if "data" in step and step["data"]:
            data = {}
            if "action" in step["data"]:
                data["action"] = step["data"]["action"]
            if "expected_result" in step["data"] and step["data"]["expected_result"]:
                data["expected_result"] = step["data"]["expected_result"]
            if data:
                clean_step["data"] = data

        if "execution" in step and step["execution"]:
            exec_cleaned = clean_execution(step["execution"])
            if exec_cleaned:
                clean_step["execution"] = exec_cleaned

        if "steps" in step and step["steps"]:
            nested = clean_steps(step["steps"])
            if nested:
                clean_step["steps"] = nested

        if clean_step:
            cleaned.append(clean_step)

    return cleaned


def clean_attachments(attachments: list) -> list:
    """Keep only file_name and mime_type from attachments."""
    cleaned = []
    for att in attachments:
        clean_att = {}
        if "file_name" in att and att["file_name"]:
            clean_att["file_name"] = att["file_name"]
        if "mime_type" in att and att["mime_type"]:
            clean_att["mime_type"] = att["mime_type"]
        if clean_att:
            cleaned.append(clean_att)
    return cleaned


def clean_relations(relations: dict) -> dict | None:
    """Clean relations: remove public_id from suite data."""
    if not relations:
        return None
    cleaned = {}
    if "suite" in relations and relations["suite"]:
        suite = relations["suite"]
        if "data" in suite and suite["data"]:
            clean_data = []
            for item in suite["data"]:
                clean_item = {}
                if "title" in item:
                    clean_item["title"] = item["title"]
                if clean_item:
                    clean_data.append(clean_item)
            if clean_data:
                cleaned["suite"] = {"data": clean_data}
    return cleaned if cleaned else None


def clean_result(result: dict) -> dict:
    """Clean a single test result."""
    cleaned = {}

    # Keep core fields
    for key in ["title", "signature", "status"]:
        if key in result and result[key]:
            cleaned[key] = result[key]

    # Keep testops_ids (skip singular testops_id)
    if "testops_ids" in result and result["testops_ids"]:
        cleaned["testops_ids"] = result["testops_ids"]

    # Keep non-empty fields
    if "fields" in result and result["fields"]:
        cleaned["fields"] = result["fields"]

    # Keep non-empty params
    if "params" in result and result["params"]:
        cleaned["params"] = result["params"]

    # Keep non-empty param_groups
    if "param_groups" in result and result["param_groups"]:
        cleaned["param_groups"] = result["param_groups"]

    # Clean relations (remove public_id)
    if "relations" in result and result["relations"]:
        rel = clean_relations(result["relations"])
        if rel:
            cleaned["relations"] = rel

    # Clean steps
    if "steps" in result and result["steps"]:
        steps = clean_steps(result["steps"])
        if steps:
            cleaned["steps"] = steps

    # Clean attachments
    if "attachments" in result and result["attachments"]:
        atts = clean_attachments(result["attachments"])
        if atts:
            cleaned["attachments"] = atts

    # Skip execution (keep only top-level status which is already in "status" field)
    # Skip muted: false (default value)
    # Skip message (dynamic timestamps, unstable whitespace)
    # Skip stacktrace (absolute file paths)
    # Skip testops_id (singular, redundant with testops_ids)

    # Keep muted only when true
    if "muted" in result and result["muted"]:
        cleaned["muted"] = result["muted"]

    return cleaned


def clean_expected(data: dict) -> dict:
    """Clean the entire expected data structure."""
    cleaned = {}

    # Keep run stats
    if "run" in data:
        cleaned["run"] = data["run"]

    # Clean results
    if "results" in data:
        cleaned["results"] = [clean_result(r) for r in data["results"]]

    return cleaned


def main():
    parser = argparse.ArgumentParser(description="Clean expected YAML files")
    parser.add_argument(
        "--input",
        required=True,
        help="Path to the expected YAML file to clean",
    )
    parser.add_argument(
        "--output",
        help="Path to write cleaned YAML (defaults to overwriting input)",
    )
    args = parser.parse_args()

    input_path = Path(args.input)
    output_path = Path(args.output) if args.output else input_path

    if not input_path.exists():
        print(f"Error: {input_path} does not exist", file=sys.stderr)
        sys.exit(1)

    with open(input_path) as f:
        data = yaml.safe_load(f)

    cleaned = clean_expected(data)

    with open(output_path, "w") as f:
        yaml.dump(cleaned, f, default_flow_style=False, allow_unicode=True, sort_keys=False)

    print(f"Cleaned expected file written to {output_path}")


if __name__ == "__main__":
    main()
