from pathlib import Path

root = Path(r"C:/Users/mouns/OneDrive/Bureau/PFMDEV-FINAL-VERSION-V2/PFMDEV-FINAL-VERSION")

CSS_FILES = [root / "public/css/main.css"]

blade_files = list((root / "resources/views").rglob("*.blade.php"))


def strip_comments(lines):
    in_comment = False
    out = []
    for line in lines:
        new_line = ""
        i = 0
        while i < len(line):
            if not in_comment and line[i:i+2] == "/*":
                in_comment = True
                i += 2
                continue
            if in_comment and line[i:i+2] == "*/":
                in_comment = False
                i += 2
                continue
            if not in_comment:
                new_line += line[i]
            i += 1
        out.append(new_line)
    return out


def parse_css(lines, base_line, file_path):
    clean_lines = strip_comments(lines)
    stack = []  # entries: {'type': 'atrule'|'rule', 'selector': str, 'seen': {prop: (line_no, line_text)}}
    pending_selector = ""
    results = []

    def current_selector_path(rule_selector):
        at_rules = [s['selector'] for s in stack if s['type'] == 'atrule']
        if at_rules:
            return " -> ".join(at_rules + [rule_selector])
        return rule_selector

    for idx, raw_line in enumerate(clean_lines):
        line_no = base_line + idx
        line = raw_line
        # If inside rule and line has property without braces
        if '{' not in line and '}' not in line:
            if stack and stack[-1]['type'] == 'rule':
                stripped = line.strip()
                if stripped and ':' in stripped and stripped.endswith(';'):
                    prop = stripped.split(':', 1)[0].strip().lower()
                    if prop:
                        seen = stack[-1]['seen']
                        if prop in seen:
                            prev_line_no, prev_line_text, rule_selector = seen[prop]
                            results.append({
                                'file': str(file_path),
                                'selector': current_selector_path(rule_selector),
                                'line_no': prev_line_no,
                                'line_text': prev_line_text.rstrip('\n')
                            })
                        seen[prop] = (line_no, lines[idx].rstrip('\n'), stack[-1]['selector'])
            continue

        # Process line with braces
        i = 0
        segment = ""
        while i < len(line):
            ch = line[i]
            if ch == '{':
                pending_selector += segment
                selector_text = pending_selector.strip()
                pending_selector = ""
                segment = ""
                if selector_text:
                    if selector_text.startswith('@'):
                        stack.append({'type': 'atrule', 'selector': selector_text, 'seen': {}})
                    else:
                        stack.append({'type': 'rule', 'selector': selector_text, 'seen': {}})
                i += 1
                continue
            if ch == '}':
                pending_selector += segment
                pending_selector = ""
                segment = ""
                if stack:
                    stack.pop()
                i += 1
                continue
            segment += ch
            i += 1

        if segment:
            if not stack or stack[-1]['type'] != 'rule':
                pending_selector += segment + "\n"
            else:
                stripped = segment.strip()
                if stripped and ':' in stripped and stripped.endswith(';'):
                    prop = stripped.split(':', 1)[0].strip().lower()
                    if prop:
                        seen = stack[-1]['seen']
                        if prop in seen:
                            prev_line_no, prev_line_text, rule_selector = seen[prop]
                            results.append({
                                'file': str(file_path),
                                'selector': current_selector_path(rule_selector),
                                'line_no': prev_line_no,
                                'line_text': prev_line_text.rstrip('\n')
                            })
                        seen[prop] = (line_no, lines[idx].rstrip('\n'), stack[-1]['selector'])
    return results


def parse_style_blocks_in_blade(file_path):
    text = file_path.read_text(encoding='utf-8', errors='ignore')
    lines = text.splitlines(keepends=True)
    results = []
    start_indices = []
    for i, line in enumerate(lines):
        if '<style' in line:
            start_indices.append(i)
    for start in start_indices:
        end = None
        for j in range(start + 1, len(lines)):
            if '</style>' in lines[j]:
                end = j
                break
        if end is None:
            continue
        css_lines = lines[start + 1:end]
        if not css_lines:
            continue
        base_line = start + 2
        results.extend(parse_css(css_lines, base_line, file_path))
    return results


all_results = []

for css_file in CSS_FILES:
    if css_file.exists():
        css_lines = css_file.read_text(encoding='utf-8', errors='ignore').splitlines(keepends=True)
        all_results.extend(parse_css(css_lines, 1, css_file))

for bf in blade_files:
    all_results.extend(parse_style_blocks_in_blade(bf))

all_results.sort(key=lambda r: (r['file'], r['line_no']))

for r in all_results:
    print(f"{r['file']}|{r['line_no']}|{r['selector'].strip()}|{r['line_text'].rstrip()}")
