#### [七牛文档](https://developer.qiniu.com/sdk#official-sdk)
#### [多媒体API](https://developer.qiniu.com/dora)
~~~
具体参照文档,以下只是简单示例
~~~
##### 图片信息 `imageInfo`
```
requst   : http://domain.com/key?imageInfo
response :
{
    "size": 231804,
    "format": "jpeg",
    "width": 1415,
    "height": 1440,
    "colorModel": "ycbcr",
    "orientation": "Unknown value 0"
}
```

##### 视频信息 `avinfo`
```
requst   : http://domain.com/key?avinfo
response :
{
    "streams": [
        {
                "index": 0,
                "codec_name": "h264",
                "codec_long_name": "H.264 / AVC / MPEG-4 AVC / MPEG-4 part 10",
                "profile": "High",
                "codec_type": "video",
                "codec_time_base": "1/60",
                "codec_tag_string": "avc1",
                "codec_tag": "0x31637661",
                "width": 540,
                "height": 960,
                "coded_width": 544,
                "coded_height": 960,
                "has_b_frames": 2,
                "pix_fmt": "yuv420p",
                "level": 31,
                "chroma_location": "left",
                "refs": 1,
                "is_avc": "true",
                "nal_length_size": "4",
                "r_frame_rate": "30/1",
                "avg_frame_rate": "30/1",
                "time_base": "1/15360",
                "start_pts": 0,
                "start_time": "0.000000",
                "duration_ts": 226314,
                "duration": "14.733984",
                "bit_rate": "469355",
                "bits_per_raw_sample": "8",
                "nb_frames": "442",
                "disposition": {
                        "default": 1,
                        "dub": 0,
                        "original": 0,
                        "comment": 0,
                        "lyrics": 0,
                        "karaoke": 0,
                        "forced": 0,
                        "hearing_impaired": 0,
                        "visual_impaired": 0,
                        "clean_effects": 0,
                        "attached_pic": 0,
                        "timed_thumbnails": 0
                    },
            "tags": {
                    "language": "und",
                    "handler_name": "VideoHandler"
                }
        },
        {
            "index": 1,
            "codec_name": "aac",
            "codec_long_name": "AAC (Advanced Audio Coding)",
            "profile": "HE-AAC",
            "codec_type": "audio",
            "codec_time_base": "1/44100",
            "codec_tag_string": "mp4a",
            "codec_tag": "0x6134706d",
            "sample_fmt": "s16p",
            "sample_rate": "44100",
            "channels": 2,
            "channel_layout": "stereo",
            "bits_per_sample": 0,
            "r_frame_rate": "0/0",
            "avg_frame_rate": "0/0",
            "time_base": "1/44100",
            "start_pts": 0,
            "start_time": "0.000000",
            "duration_ts": 648182,
            "duration": "14.698005",
            "bit_rate": "64328",
            "max_bit_rate": "64328",
            "nb_frames": "319",
            "disposition": {
                    "default": 1,
                    "dub": 0,
                    "original": 0,
                    "comment": 0,
                    "lyrics": 0,
                    "karaoke": 0,
                    "forced": 0,
                    "hearing_impaired": 0,
                    "visual_impaired": 0,
                    "clean_effects": 0,
                    "attached_pic": 0,
                    "timed_thumbnails": 0
                },
            "tags": {
                "language": "und",
                "handler_name": "SoundHandler"
            }
        }
    ],
    "format": {
            "nb_streams": 2,
            "nb_programs": 0,
            "format_name": "mov,mp4,m4a,3gp,3g2,mj2",
            "format_long_name": "QuickTime / MOV",
            "start_time": "0.000000",
            "duration": "14.813000",
            "size": "996324",
            "bit_rate": "538080",
            "probe_score": 100,
            "tags": {
            "major_brand": "isom",
            "minor_version": "512",
            "compatible_brands": "isomiso2avc1mp41",
            "title": "''",
            "encoder": "Lavf58.20.100",
            "comment": "''"
        }
    }
}
```

##### 获取视频作为图片
```
format : http://domain.com/key?vframe/[jpg|png]/(offset/秒数)/(w/宽度)/(h/高度)/(rotate/旋转)
img    : http://domain.com/key?vframe/jpg/offset/0
imgAll : http://domain.com/key?vframe/jpg/offset/0/w/960/h/1280/rotate/90
```