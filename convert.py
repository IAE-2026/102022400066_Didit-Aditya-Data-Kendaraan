import os

try:
    # Read output.txt with UTF-16 encoding and write to output_utf8_clean.txt with UTF-8
    input_path = "../output.txt"
    output_path = "output_utf8_clean.txt"
    
    if os.path.exists(input_path):
        with open(input_path, "r", encoding="utf-16") as infile:
            content = infile.read()
        with open(output_path, "w", encoding="utf-8", errors="ignore") as outfile:
            outfile.write(content)
        print("Success: Converted output.txt to output_utf8_clean.txt")
    else:
        print("Error: output.txt not found")
except Exception as e:
    print(f"Error: {e}")
