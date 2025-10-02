import tarfile
import os
import shutil

tar_file = r"C:\laragon\www\nativephp-switch-driver-sql\static-php-cli\downloads\temp\php-8.3.26.tar"
target_dir = r"C:\laragon\www\nativephp-switch-driver-sql\static-php-cli\source\php-src"

print(f"Extracting {tar_file} to {target_dir}")

# Ensure target directory exists
os.makedirs(target_dir, exist_ok=True)

# Open and extract tar file
with tarfile.open(tar_file, 'r') as tar:
    # Get all members
    members = tar.getmembers()

    # Extract with strip-components=1 logic
    for member in members:
        # Skip the root directory (first component)
        parts = member.name.split('/', 1)
        if len(parts) > 1:
            member.name = parts[1]
            tar.extract(member, target_dir)
            if len(tar.getmembers()) < 100:  # Only print for first few
                print(f"  Extracted: {member.name[:50]}")

print(f"\nExtraction completed!")
print(f"Verifying: {os.path.exists(os.path.join(target_dir, 'main', 'php_version.h'))}")