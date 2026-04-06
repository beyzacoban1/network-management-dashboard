# network-management-dashboard
A PHP-based Network Monitoring System (NMS) utilizing SNMP for real-time device health tracking, featuring Python-based security tools for port scanning and network stress testing.
# Network Management & Monitoring Dashboard
This project is a comprehensive Network Management System (NMS) designed to monitor the performance and security of multiple devices within a local network in real-time.

## Key Technical Features:

* SNMP-Based Data Collection: Uses SNMP (Simple Network Management Protocol) to remotely fetch CPU load and RAM utilization directly from network nodes.

* Active Device Discovery: Implements ICMP-based status checks (Ping) to monitor device availability and network latency.

* Automated Port Scanning: Regularly audits critical ports (80, 443, 445, 3389) to identify potential security exposures.

* Live Data Visualization: A dynamic web interface featuring Chart.js to visualize historical performance trends of devices like Beyza_PC, Damla_PC, and Ilayda_PC.

* Relational Logging: Stores granular performance metrics in a MySQL database for historical analysis.

  ## Project Interface
![Dashboard Screen](./assets/dashboard-live.png)
