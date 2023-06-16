import subprocess

command = ['git', 'log', '--pretty=format:%s']

output = subprocess.check_output(command)

with open('./docs/commits.md', 'w') as file:
    file.write("# Commits\n\n")
    file.write(output.decode('utf-8'))
