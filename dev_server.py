#!/usr/bin/env python3
import http.server
import socketserver
import os
import re
import html
from urllib.parse import unquote

class SSIHandler(http.server.SimpleHTTPRequestHandler):
    def do_GET(self):
        # Decode URL
        path = unquote(self.path)
        if path.endswith('/'):
            path += 'index.html'
        

        if path.startswith('/'):
            path = path[1:]
        

        if '..' in path:
            self.send_error(403)
            return
            
        filepath = os.path.join(os.getcwd(), path)
        
        if os.path.exists(filepath) and os.path.isfile(filepath):
            if filepath.endswith('.html') or filepath.endswith('.shtml'):
                self.serve_ssi_file(filepath)
            else:
                super().do_GET()
        else:
            super().do_GET()
    
    def serve_ssi_file(self, filepath):
        try:
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Process SSI includes
            content = self.process_ssi(content, getattr(self, 'server_port', 8002))
            
            self.send_response(200)
            self.send_header('Content-type', 'text/html; charset=utf-8')
            self.send_header('Content-Length', str(len(content.encode('utf-8'))))
            self.end_headers()
            self.wfile.write(content.encode('utf-8'))
            
        except Exception as e:
            self.send_error(500, f"Error processing SSI: {str(e)}")
    
    def process_ssi(self, content, current_port=8002):
        # Process <!--#include virtual="..." -->
        def include_handler(match):
            include_path = match.group(1)
            if include_path.startswith('/'):
                include_path = include_path[1:]
            
            full_path = os.path.join(os.getcwd(), include_path)
            
            if os.path.exists(full_path):
                try:
                    with open(full_path, 'r', encoding='utf-8') as f:
                        content = f.read()
                        return html.unescape(content)
                except:
                    return f"<!-- Error reading {include_path} -->"
            else:
                return f"<!-- File not found: {include_path} -->"
        
        # Process <!--#echo var="..." -->
        def echo_handler(match):
            var_name = match.group(1)
            if var_name == "SERVER_NAME":
                return f"localhost:{current_port}"
            elif var_name == "DOCUMENT_URI":
                return "/"
            return f"<!-- Unknown variable: {var_name} -->"
        
        # Remove all SSI directives except includes and echo
        content = re.sub(r'<!--#(?!include|echo)[^>]*-->', '', content)
        
        content = re.sub(r'<!--#include virtual="([^"]+)" -->', include_handler, content)
        content = re.sub(r'<!--#echo var="([^"]+)" -->', echo_handler, content)
        
        return content

if __name__ == "__main__":
    PORT = 8002
    
    # Try to find an available port
    for port in range(PORT, PORT + 10):
        try:
            class PortAwareSSIHandler(SSIHandler):
                server_port = port
            
            with socketserver.TCPServer(("", port), PortAwareSSIHandler) as httpd:
                print(f"Serveur de développement démarré sur http://localhost:{port}")
                print("Appuyez sur Ctrl+C pour arrêter")
                try:
                    httpd.serve_forever()
                except KeyboardInterrupt:
                    print("\nServeur arrêté")
                break
        except OSError:
            continue
    else:
        print(f"Impossible de trouver un port libre entre {PORT} et {PORT + 9}")