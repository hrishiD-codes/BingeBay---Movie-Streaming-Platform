'use client';

import { useRef, useEffect } from 'react';
import { Play, Pause, Volume2, VolumeX, Maximize, Minimize } from 'lucide-react';

export default function VideoPlayer({ streamUrl, poster, title }) {
  const videoRef = useRef(null);
  const containerRef = useRef(null);

  useEffect(() => {
    const video = videoRef.current;
    if (!video) return;

    // Load video metadata
    video.load();
  }, [streamUrl]);

  const togglePlay = () => {
    const video = videoRef.current;
    if (video.paused) {
      video.play();
    } else {
      video.pause();
    }
  };

  const toggleMute = () => {
    const video = videoRef.current;
    video.muted = !video.muted;
  };

  const toggleFullscreen = () => {
    const container = containerRef.current;
    
    if (!document.fullscreenElement) {
      container.requestFullscreen();
    } else {
      document.exitFullscreen();
    }
  };

  return (
    <div 
      ref={containerRef}
      className="relative w-full max-w-6xl mx-auto bg-black rounded-lg overflow-hidden shadow-2xl"
    >
      <video
        ref={videoRef}
        className="w-full aspect-video"
        poster={poster}
        controls
        controlsList="nodownload"
        preload="metadata"
      >
        <source src={streamUrl} type="video/mp4" />
        Your browser does not support the video tag.
      </video>
      
      {title && (
        <div className="absolute top-0 left-0 right-0 bg-gradient-to-b from-black/80 to-transparent p-6">
          <h2 className="text-2xl font-bold text-white">{title}</h2>
        </div>
      )}
    </div>
  );
}
