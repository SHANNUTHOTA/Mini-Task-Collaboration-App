<style>
  footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: linear-gradient(to right, #2c3e50, #34495e);
    color: #ecf0f1;
    text-align: center;
    padding: 12px 15px;
    font-size: 1rem;
    z-index: 1000;
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.2);
  }

  footer p {
    margin: 0;
    font-weight: 500;
    letter-spacing: 0.5px;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
  }

  @media (max-width: 768px) {
    footer {
      font-size: 0.9rem;
      padding: 10px;
    }
  }
</style>

<footer>
  <p>&copy; <?php echo date("Y"); ?> Task Collaboration App. All rights reserved.</p>
</footer>
